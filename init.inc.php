<?php 

//if (!defined('ROOTPATH')) 
//	die('no key defined!');

// Обьявить пространство имен для защиты от запуска из любого класа из любой точки

spl_autoload_register(function ($class_name) {

	// TODO: Использовать замыкание для отловки ошибки

	$flag = false;

	// Указанна папка => префикс файла

	$folders = array(
		'Includes' 	 => '.inc.',
		'Classes' 	 => '.class.',
		'Controller' => '.ctrl.'
	);

	try {
		foreach ($folders as $key => $value) {

			$path = ROOTPATH.$key.DS.$class_name.$value.'php'; 
			$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);

			if (file_exists($path)) {
				$flag = true; 
				break;
			} 
		}
		
		if (!$flag) {
			throw new Exception("Не могу продолжить, не найдет указанный класс. Выхожу...");
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
					$in[$key] = gettype($value).' => '.$value.'<br />';
				}
			}
		} else {

			$in = gettype($in).' => '.$in;
		}

		// TOODO: проверка на обьект is_object($string)

		echo '<i>Откуда запущенно:</i> <b>'.$funcname.' </b></br>';

		if (empty($in) || !$in) {

			var_dump($in);
		} else {
			print_r($in);
		}

		//$debug ? var_dump($in) : print_r($in);

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