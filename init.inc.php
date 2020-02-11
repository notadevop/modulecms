<?php 

if (!defined('ROOTPATH')) 
	die('no key defined!');

spl_autoload_register(function ($class_name) {

	// TODO: Использовать замыкание для отловки ошибки

	$flag = false;

	$folders = array(
		'Includes' 	=> '.inc.',
		'Classes' 	=> '.class.'
	);

	try {
		foreach ($folders as $key => $value) {
			$path = ROOTPATH.$key.DS.$class_name.$value.'php'; 
			if (file_exists($path)) {
				$flag = true; 
			} 
		}
		if (!$flag) {
			throw new Exception("Не найден системный класс. Выхожу...");
		}

		require_once ( $path );	
	
	} catch (Exception $e) {

		$msg = $e->getMessage();

		die(debugger($msg));
	}
}); 


function debugger($input, string $funcname = '', bool $debug = false): void {

	/*
	if(empty($input)) { 

		print_r('Пройденно....'.$funcname);
	}
	*/
	// Для определения откуда запускается дебаггер вставляем в funcname = __FUNCTION__
	// Либо если метод нужен с полным путем то __METHOD__

	// try catch для методов или функций

	$clos = function($in) use (&$clos, &$debug, &$funcname) {

		$array = false;
		$string = '';

		if(is_array($in)) {
			foreach ($in as $key => $value) {
				if (is_array($value)) {
					$clos($value);
				} else {
					$string .= gettype($value).': ['.$key.'] => '.$value.'<br />';
				}
			}
		} else {
			$string = $in;	
		}

		// TOODO: проверка на обьект is_object($string)

		$string = '<i>Откуда инфо:</i> <b>'.$funcname.' </b></br>'.$string;

		$debug ? var_dump($string) : print_r($string);

		/*
		$filename = "./debugfile.txt";
		$fh = fopen($filename, "a");
		fwrite($fh, 'function: '.$funcname.' => '.$string."\n");
		fclose($fh);
		*/
		//file_put_contents('./debugfile.txt', $string, FILE_APPEND | LOCK_EX);
	};

	echo '<pre>';
	if (!empty($input)) {

		$clos($input); 
	} else {
		//echo 'Дан пустой параметр, тип переменной => <b>'. gettype($input).'</b>';
	}
	echo '</pre>';
}