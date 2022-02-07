<?php 

(defined('ROOTPATH') && defined('DS')) or die('something wrong');


// Временно, для использования заменить на FileManipulator!

(function ():void {

	$file = '.htaccess';

	$data = '
		Options -MultiViews
		RewriteEngine On
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteRule ^ index.php [QSA,L]
	';

	if (!file_exists(ROOTPATH . $file)) {
		file_put_contents($file, $data);
	}
})();


foreach ((function (): array {
	return array(
		'Includes/settings.inc.php',
		'Includes/Library.inc.php',

		'LangLibrary/Ru-ru/Attentions.lang.php',
		'LangLibrary/Ru-ru/Info.lang.php',
		'LangLibrary/Ru-ru/Errors.lang.php',
		'LangLibrary/Ru-ru/Staticlinks.lang.php',
	);
})() as $key => $value) {
	
	try {
		if (!file_exists(ROOTPATH.$value)) {
			throw new Exception('Core file not found!', 1);
		}
		
		require_once (ROOTPATH.$value);

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


