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
				<form action="index.php">
					<button type="submit" class="button" name="auction" value="generate">Сгенерировать файлы</button>
				</form>
			</div>
			<div>
				<form action="index.php">
					<input type="text" name="text" />
					<button type="submit" class="button" name="auction" value="unification">Объединить файлы</button>
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