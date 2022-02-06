<?php 

(defined('ROOTPATH') && defined('DS')) or die('something wrong');


$files = array(

	'Includes/settings.inc.php',
	'Includes/Library.inc.php',

	'languagePack/Ru-ru/Attentions.lang.php',
	'LanguagePack/Ru-ru/Info.lang.php',
	'LanguagePack/Ru-ru/Errors.lang.php',
	'LanguagePack/Ru-ru/Staticlinks.lang.php',
);

foreach ($files as $key => $value) {

	$r = ROOTPATH . $value;

	try {
		if (!file_exists($r)) {
			throw new Exception('Core file not found!', 1);
		}
		
		require_once ($r);

	} catch (Exception $e) {
		die($e->getMessage());
	}
}




class ClassNotFoundException extends Exception { }

// Обьявить пространство имен для защиты от запуска из любого класа из любой точки

// Проверяет все варианты указанные выше в переменной $folders 
// и после этого только когда находит файл тогда проверяет класс
// TODO: Использовать замыкание для отловки ошибки
// Указанна папка => префикс файла

spl_autoload_register(function ($class_name) {

	$found = false;

	$folders = array(
		'Includes' 	 		=> '.inc.',
		'Classes' 	 		=> '.class.',
		'Controller' 		=> '.ctrl.',   // Удалить поже!!!
		'Bin'				=> '.bin.',
		'viewControllers'	=> '.contrl.',
		'Classes/Protectors'=> '.class.',
		'Classes/Priveleges'=> '.class.',
	);

	try {
		foreach ($folders as $key => $value) {

			$path = ROOTPATH.$key.DS.$class_name.$value.'php'; 
			
			// delete???
			//$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);

			if (file_exists($path)) { 
				require_once ($path);
				if (class_exists($class_name)) { 
					$found = true;
					break; 
				}
			} 
		}

		if (!$found) {
			throw new ClassNotFoundException(sprintf(ERRCLASSNOTFOUND, $class_name), 1);
		}
	
	} catch (ClassNotFoundException $e) {
		die(debugger($e->getMessage(), __FUNCTION__));
	}
}); 


