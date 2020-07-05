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


 // Для определения метода или функции __METHOD__, __FUNCTION__

function debugger($input, $param=__FUNCTION__, $vardump=false): void {

	echo '<pre><hr/>';

	if ($param != false)
		echo '</br>Дебаггер запущен из =>'.$param ;	

	if ($vardump) {

		echo '<br/>';
		var_dump($input);
		echo '</pre>';
		return;
	}

	echo '<br/>';
	print_r($input);
	echo '</pre><hr/>';
}

// 
function console($input, $param='', $doAction) { }


