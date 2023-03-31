<?php
class Generation {
			
	function dateGeneration() {
		$dt_to = time();
		$dt_from = strtotime('-5 years',time());
		return date("d.m.Y", mt_rand($dt_from, $dt_to));
	}
	
	function latStringGeneration() {
		$permitted_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		return substr(str_shuffle($permitted_chars), 0, 10);
		
	}
	
	function rusStringGeneration() {
		$permitted_chars = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ';
		return substr(str_shuffle(iconv('utf-8', 'cp1251', $permitted_chars)), 0, 10);
		
	}
	
	function intNumberGeneration() {
		$number = mt_rand(1, 100000000);
		
		if( $number%2 == 0 ){
			return $number;
		} else {
			return $this->intNumberGeneration();
		}
	}
	
	function floatNumberGeneration() {
		$float_part = mt_rand(0, mt_getrandmax())/mt_getrandmax();
		$integer_part = mt_rand(1, 20);
		$float_number = $integer_part + $float_part;
		return number_format((float)$float_number, 8);
	}
	
	function stringGeneration() {
		$string = $this->dateGeneration()."||".$this->latStringGeneration()."||".$this->rusStringGeneration()."||".$this->intNumberGeneration()."||".$this->floatNumberGeneration()."||\n";
		
		return $string;
	}
	
	function fileWrite($file_num) {
		$filename = '../files/file'.$file_num.'.txt';
		
		$fh = fopen($filename, 'a'); 
		
		for ($i=0; $i<100000; $i++) {
			$string = $this->stringGeneration();
			fwrite($fh, $string);
		}
		fclose($fh);
	}
	
	function filesGeneration() {
		for($i=1; $i<=100; $i++) {
			$this->fileWrite($i);
			echo "создан файл ". $i ." из 100<br>";
		}
	}
}

class Unification {
	function unificationFiles($text) {
		$save = fopen('../files/unification.txt', 'w');
		
		// Задаём директорию с файлами
		$dir = opendir('../files');
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
								
				fclose($f);
			}
		}
		fclose($save);
		
		return $count;
	}
}

class DataBase {
	function DataBaseWrite($filename, $tempname) {
		$link = mysqli_connect("localhost", "root", "root", "b1");
		
		$count = 0;
		$linesCount = $this->linesCount($tempname);
		$f = fopen($tempname, 'r');
		while (($buffer = fgets($f)) !== false) {
			
			$myArray = explode('||', $buffer ?? '');			
				
			$sql = 'INSERT INTO files (file_name, date, lat_symbol, rus_symbol, int_number, float_number) VALUES ("'. $filename .'", "'. date('Y-m-d', strtotime(str_replace('.', '/', $myArray[0]))) . '", "' . $myArray[1] .'", "'. iconv("cp1251", "utf-8", $myArray[2]) .'", "'. $myArray[3] .'", "' . $myArray[4] .'")';
			
			$result = mysqli_query($link, $sql);
			
			$count++;
			
			echo "В базу данных записано " . $count . " строк из " . $linesCount . "<br>";
		}
		
		mysqli_close($link);
	}
	
	function linesCount($file) 
	{ 
		// в начале ищем сам файл. Может быть, путь к нему был некорректно указан 
		if(!file_exists($file))exit("Файл не найден"); 
		 
		// рассмотрим файл как массив
		$file_arr = file($file); 
		 
		// подсчитываем количество строк в массиве 
		$lines = count($file_arr); 
		 
		// вывод результата работы функции 
		return $lines-1; 
	} 
	
	function createTotalProcedure() {
		$link = mysqli_connect("localhost", "root", "root", "b1");
		
		$sql = 'DROP PROCEDURE IF EXISTS "TOTAL";

				DELIMITER //

				CREATE PROCEDURE TOTAL(IN f_name VARCHAR(128))
				BEGIN
				SELECT SUM(int_number) FROM b1.files
				WHERE file_name = f_name;
				END //

				DELIMITER ;';
		
		$result = mysqli_query($link, $sql);
		
		mysqli_close($link);
	}
	
	function createMedianProcedure() {
		$link = mysqli_connect("localhost", "root", "root", "b1");
		
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
		
		$result = mysqli_query($link, $sql);
		
		mysqli_close($link);
	}
	
	function totalInt($file_name) {
		$link = mysqli_connect("localhost", "root", "root", "b1");
		
		$sql = 'CALL TOTAL("'.$file_name.'")';
			
		$result = mysqli_query($link, $sql);
		
		mysqli_close($link);
			
	}
	
	function medianFloat($file_name) {
		$link = mysqli_connect("localhost", "root", "root", "b1");
		
		$sql = 'CALL MEDIAN("'.$file_name.'")';
			
		$result = mysqli_query($link, $sql);
		
		mysqli_close($link);
	}
}

// В зависимости от атрибута в адресе запускаем необходимую функцию
if (isset($_POST['action'])) {
	switch ($_POST['action']) {
		case 'generate':
			$st = new Generation();
			$st->filesGeneration();
			break;
		case 'unification':
			if (isset($_POST['text'])) {
				$un = new Unification();
				$un->unificationFiles($_POST['text']);
				return;
			}
			break;
		case 'importtodb':
			$filename = basename($_FILES['uploadedFile']['name']);
			$tempname = $_FILES['uploadedFile']['tmp_name'];
			
			$db = new DataBase();
			$db->DataBaseWrite($filename, $tempname);
			break;
		case 'totalint':
			$db = new DataBase();
			$db->totalInt($_POST['file_name']);
			break;
		case 'medianfloat':
			$db = new DataBase();
			$db->medianFloat($_POST['file_name']);
			break;
	}
}
?>