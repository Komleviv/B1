<?php
//include 'src/classes.php';
?>
<!DOCTYPE html>
<html lang="ru-RU">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;700&display=swap" rel="stylesheet">
		<link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.css" rel="stylesheet">
		<link href="src/b1.css" rel="stylesheet">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
		<title>Тестовое задание B1</title>
	</head>
	<body>
		<div class="container">
			<div>
				<b>Задание 1.<b><br>
				<p>Сгенерировать 100 текстовых файлов с заданной структурой, каждый из которых содержит 100 000 строк.</p>
			</div>
			<div class="task">
				<form action="index.php">
					<button type="submit" class="button" name="action" value="generate">Сгенерировать файлы</button>
				</form>
			</div>
			<div>
				<b>Задание 2.<b><br>
				<p>Реализовать объединение файлов в один. При объединении должна быть возможность удалить из всех файлов строки с заданным сочетанием символов, например, «abc» с выводом информации о количестве удаленных строк</p>
			</div>
			<div class="task">
				<form action="index.php">
					<label for="text">Cимволы для удаления: </label>
					<input type="text" name="text" id="text" />
					<button type="submit" class="button" name="action" value="unification">Объединить файлы</button>
				</form>
			</div>
			<div>
				<b>Задание 3.<b><br>
				<p>Создать процедуру импорта файлов с таким набором полей в таблицу в СУБД. При импорте должен выводится ход процесса (сколько строк импортировано, сколько осталось)</p>
			</div>
			<div class="task">
				<form method="post" action="src/classes.php" enctype = 'multipart/form-data'>
					<input type="file" name="uploadedFile" />
					<button type="submit" class="button" name="action" value="importtodb">Импортировать файл</button>
				</form>
			</div>
			<div>
				<b>Задание 4.<b><br>
				<p>Реализовать хранимую процедуру в БД (или скрипт с внешним sql-запросом), который считает сумму всех целых чисел и медиану всех дробных чисел</p>
			</div>
			<div class="task">
				<form method="post" action="src/classes.php">
					<label for="file_name">Выбере файл:</label>
					<select name="file_name" id="file_name">
						<?php
						$link = mysqli_connect("localhost", "root", "root", "b1");
						$sql = 'SELECT file_name FROM files GROUP BY file_name';
						$result = mysqli_query($link, $sql);

						if ($result == false) {
							print("Произошла ошибка при выполнении запроса");
						}
						
						foreach ($result as $r) {
							echo '<option value="'. $r["file_name"] .'">'. $r["file_name"] .'</option>';
						}
						
						mysqli_close($link);
					  ?>
				  </select>
					<button type="submit" class="button" name="action" value="totalint">Посчитать сумму челых чисел</button>
					<button type="submit" class="button" name="action" value="medianfloat">Посчитать медиану дробных чисел</button>
				</form>
			</div>
		</div>
		<script>
			$(document).ready(function(){
				$('.button').click(function(){
					var clickBtnValue = $(this).val();
					var ajaxurl = 'src/classes.php',
					data =  {'action': clickBtnValue};
					$.post(ajaxurl, data, function (response) {
						// Response div goes here.
						alert("Действие выполнено успешно");
					});
				});
			});
		</script>
	</body>
</html>