<?php
// Класс генерации файлов
class Generation {
		
	// Функция генерации произвольной даты от текущего даты до даты 5 лет назад
	function dateGeneration() {
		$dt_to = time(); 						    //текущая дата
		$dt_from = strtotime('-5 years',time());    //дата 5 лет назад
		
		// Возврощаем произвольную дату в указанном диапазоне в формате день.месяц.год_полностью
		return date("d.m.Y", mt_rand($dt_from, $dt_to));
	}
	
	// Функция генерации набора из 10 случайных латинских символов
	function latStringGeneration() {
		// строка, содержащая все буквы латинского алфавита в обоих регистрах
		$permitted_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
		
		// Возвращаем строку из 10 произвольных символов
		return substr(str_shuffle($permitted_chars), 0, 10);		
	}
	
	// Функция генерации набора из 10 случайных русских символов
	function rusStringGeneration() {
		// строка, содержащая все буквы русского алфавита в обоих регистрах
		$permitted_chars = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ';
		
		// Возвращаем строку из 10 произвольных символов в колировки CP-1251
		return substr(str_shuffle(iconv('utf-8', 'cp1251', $permitted_chars)), 0, 10);		
	}
	
	// Функция генерации произвольных целых чётных чисел в промежутке от 1 до 100 000 000
	function intNumberGeneration() {
		// Генерируем произвольное число
		$number = mt_rand(1, 100000000);
		
		// Если оно чётное, возвращаем его. Если нечетное, перегенерируем число заново.
		if( $number%2 == 0 ){
			return $number;
		} else {
			return $this->intNumberGeneration();
		}
	}
	
	// Функция генерации произвольного положительного числа с 8 знаками после запятой в диапазоне от 1 до 20
	function floatNumberGeneration() {
		// Генерируем дробную часть от 0 до максимально возможного значение случайного числа делёного на максимально возможное значение случайного числа
		$float_part = mt_rand(0, mt_getrandmax())/mt_getrandmax();
		
		// Генерируем целую часть числа от 1 до 19
		$integer_part = mt_rand(1, 19);
		
		// Соединяем обе части
		$float_number = $integer_part + $float_part;
		
		// Возвращаем дробное число с 8 знаками после запятой
		return number_format((float)$float_number, 8);
	}
	
	// Функция создания строки из даты, сгенерированных чисел и символов
	function stringGeneration() {
		$string = $this->dateGeneration()."||".$this->latStringGeneration()."||".$this->rusStringGeneration()."||".$this->intNumberGeneration()."||".$this->floatNumberGeneration()."||\n";
		
		return $string;
	}
	
	// Функция записи 100 000 созданных строк в указанный файл
	function fileWrite($file_num) {
		$filename = '../files/file'.$file_num.'.txt'; // Имя файла для записи
		
		
		$fh = fopen($filename, 'a'); // Открываем (или создаём) файл на запись
		
		// Вызываем функцию генерации строки и записываем её в открытй файл
		for ($i=0; $i<100000; $i++) {
			$string = $this->stringGeneration();
			fwrite($fh, $string);
		}
		fclose($fh); 				// Закрываем файл
	}
	
	// Функция создания 100 файлов
	function filesGeneration() {
		
		// Последовательно берём числа от 1 до 100 и вызываем функцию записи строк в файл, передавая ей число для присвоения имени
		for($i=1; $i<=100; $i++) {
			$this->fileWrite($i);
			
			// После создания файла выводим информацию сколько файлов создано
			echo "создан файл ". $i ." из 100<br>";
		}
	}
}

// Класс объединения созданных файлов в один файл и удаления строк с указанными символами
class Unification {
	function unificationFiles($text) {
		// Открываем (или создаём) файл на запись
		$save = fopen('../files/unification.txt', 'w'); 
		
		// Задаём директорию с файлами
		$dir = opendir('../files');
		
		// Счётчик удалённых строк
		$count = 0;
		
		// Просматриваем все файлы в директории
		while (false !== ($file = readdir($dir))) {
			
			// Обрабатываем только файлы с названием 'file'
			if (strpos($file, 'file') !== false) {
				
				$f = fopen('../files/'.$file, 'r');
				
				// Задаём шаблон для поиска удаляемых символов без учёта регистра
				$pattern = "/" . $text . "/i";
				
				// Построчно читаем текущий файл
				while (($buffer = fgets($f)) !== false) {
					
						// Если в строке находим символы, соответствующие шаблону, очищаем строку и увеличиваем счётчик
						if(preg_match($pattern, $buffer, $matches)) {
							$buffer = '';		
							$count++;
						} 
						
						// Записываем оставшиеся строки в объединённый файл
						fwrite($save, $buffer);
										
				}
								
				fclose($f); // Закрываем файл из которого берём строки
			}
		}
		fclose($save); // Закрываем объединённый файл
		
		// Возвращаем количество удалённыйх строк
		return $count;
	}
}

// Класс подключения к БД и записи в неё строк из файла
class DataBase {
	// Функция подключения к серверу базы данных и выбора базы
	function dbConnect() {
		$link = mysqli_connect("localhost", "root", "root", "b1");		
		return $link;
	}
	
	// Функция построчной записи файла в базу данных.
	function DataBaseWrite($filename, $tempname) {
		$link = $this->dbConnect(); // Подключение к базе данных
		
		// Счётчик записанных строк
		$count = 0;
		
		// Получаем из функции подсчёта количество строк в файле
		$linesCount = $this->linesCount($tempname);
		
		// Открываем файл
		$f = fopen($tempname, 'r');
		
		// Построчно читаем загружаемый файл
		while (($buffer = fgets($f)) !== false) {
			
			// Формируем из строки массив, элементами которого являются значения между ||
			$myArray = explode('||', $buffer ?? '');			
			
			// Формируем SQL-запрос	на запись строки в талицу базы данных. В процессе приводим дату к формату SQL и перекодируем русские символы из CP-1251	в UTF-8	
			$sql = 'INSERT INTO files (file_name, date, lat_symbol, rus_symbol, int_number, float_number) VALUES ("'. $filename .'", "'. date('Y-m-d', strtotime(str_replace('.', '/', $myArray[0]))) . '", "' . $myArray[1] .'", "'. iconv("cp1251", "utf-8", $myArray[2]) .'", "'. $myArray[3] .'", "' . $myArray[4] .'")';
			
			// Выполняем запрос на запись в базу данных
			$result = mysqli_query($link, $sql);
			
			// Увеличиваем на 1 счётчик записанных строк
			$count++;
			
			echo "В базу данных записано " . $count . " строк из " . $linesCount . "<br>";
		}
		
		mysqli_close($link);  // Закрываем соединение с базой данных
	}
	
	function linesCount($file) 
	{ 
		// Ищем файл. Выводим ошибку, если файл не найден 
		if(!file_exists($file))exit("Файл не найден"); 
		 
		// Рассматриваем файл как массив
		$file_arr = file($file); 
		 
		// Подсчитываем количество строк в массиве 
		$lines = count($file_arr); 
		 
		// Выводим результат работы функции 
		return $lines; 
	} 
	
	// Функция создания в БД процедуры подсчёта суммы всех целых чисел указанного файла
	function createTotalProcedure() {
		$link = $this->dbConnect();  // Подключение к базе данных
		
		// Удаляем процедуру, если она есть. Если нет, создаём процедуру, которая считает сумму целых чисел указанного файла
		$sql = 'DROP PROCEDURE IF EXISTS "TOTAL";

				DELIMITER //

				CREATE PROCEDURE TOTAL(IN f_name VARCHAR(128))
				BEGIN
				SELECT SUM(int_number) FROM b1.files
				WHERE file_name = f_name;
				END //

				DELIMITER ;';
		
		// Выполняем запрос на на создание процедуры
		$result = mysqli_query($link, $sql);
		
		// Закрываем соединение с базой данных
		mysqli_close($link);
	}
	
	// Функция создания в БД процедуры подсчёта медианы всех дробных чисел указанного файла
	function createMedianProcedure() {
		$link = $this->dbConnect();  // Подключение к базе данных
		
		// Удаляем процедуру, если она есть. Если нет, создаём процедуру, которая расчитывает медиану всех дробных чисел указанного файла
		$sql = 'DROP PROCEDURE IF EXISTS "MEDIAN";

				DELIMITER //

				CREATE PROCEDURE MEDIAN()
				BEGIN
				SELECT AVG(ff.float_number) as median_val
				FROM (
				SELECT float_number, @rownum:=@rownum+1 as `row_number`, @total_rows:=@rownum
				  FROM files f, (SELECT @rownum:=0) r
				  WHERE f.float_number is NOT NULL AND f.file_name = f_name
				  ORDER BY f.float_number
				) as ff
				WHERE ff.row_number IN ( FLOOR((@total_rows+1)/2), FLOOR((@total_rows+2)/2) );
				END //

				DELIMITER ;';
		
		// Выполняем запрос на на создание процедуры
		$result = mysqli_query($link, $sql);
		
		// Закрываем соединение с базой данных
		mysqli_close($link);
	}
	
	// Функция вызова процедуры подсчёта суммы всех целых чисел файла, имя которго мы передаём как параметр
	function totalInt($file_name) {
		$link = $this->dbConnect();  // Подключение к базе данных
		
		// Формируем запрос вызова процедуры суммы всех целых чисел и передаём ей имя файла
		$sql = 'CALL TOTAL("'.$file_name.'")';
			
		// Выполняем запрос
		$result = mysqli_query($link, $sql);
		
		// Закрываем соединение с базой данных
		mysqli_close($link);
			
	}
	
	// Функция вызова процедуры подсчёта медианы всех дробных чисел файла, имя которго мы передаём как параметр
	function medianFloat($file_name) {
		$link = $this->dbConnect();  // Подключение к базе данных
		
		// Формируем запрос вызова процедуры расчёта медианы дробных чисел и передаём ей имя файла
		$sql = 'CALL MEDIAN("'.$file_name.'")';
		
		// Выполняем запрос		
		$result = mysqli_query($link, $sql);
		
		// Закрываем соединение с базой данных
		mysqli_close($link);
	}
}

// В зависимости от атрибута в адресе запускаем необходимую функцию
if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case 'generate':						// Задание 1.1 Генерация файлов
			
			// Создаём объект класса
			$st = new Generation();
			// Вызываем у объекта функцию генерации файлов
			$st->filesGeneration();
			break;
		case 'unification':						// Задание 1.2 Объединение файлов
			// Если задан атрибут text
			if (isset($_POST['text'])) {
				// Создаём объект класса
				$un = new Unification();
				// Вызываем у объекта функцию объединения файлов и передаём ей текст для поиска строк и их удаления
				$un->unificationFiles($_POST['text']);
				return;
			}
			break;
		case 'importtodb':						// Задание 1.3 Импорт файла в базу данных
			// Имя загружаемого файла
			$filename = basename($_FILES['uploadedFile']['name']);
			// Расположение файла во временном каталоге
			$tempname = $_FILES['uploadedFile']['tmp_name'];
			
			// Создаём объект класса
			$db = new DataBase();
			// Вызываем у объекта функцию записи файла в базу данных
			$db->DataBaseWrite($filename, $tempname);
			break;
		case 'totalint':						// Задание 1.4 Рассчёт суммы целых чисел
			
			// Создаём объект класса
			$db = new DataBase();
			// Вызываем у объекта функцию запуска процедуры подсчёта суммы целых чисел
			$db->totalInt($_POST['file_name']);
			break;
		case 'medianfloat':						// Задание 1.4 Рассчёт медианы дробных чисел
			
			// Создаём объект класса
			$db = new DataBase();
			// Вызываем у объекта функцию запуска процедуры расчёта медианы дробных чисел
			$db->medianFloat($_POST['file_name']);
			break;
	}
}
?>