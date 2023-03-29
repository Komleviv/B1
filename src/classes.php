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
		$filename = __DIR__ . '/file'.$file_num.'.txt';
		
		$fh = fopen($filename, 'a'); 
		
		for ($i=0; $i<100; $i++) {
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
	function unificationFiles($stringDelete) {
		$save = fopen(__DIR__ . '/unification.txt', 'w');
		$f1 = fopen(__DIR__ . '/file1.txt', 'r');
		stream_copy_to_stream($f1, $save);
		fclose($f1);
		$f2 = fopen(__DIR__ . '/file2.txt', 'r');
		stream_copy_to_stream($f2, $save);
		fclose($f2);
		fclose($save);
	}
	
	function deleteString($stringDelete) {
		
	}
}

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
			}
			break;
	}
}

$un = new Unification();
$un->unificationFiles($_POST['text']);
?>