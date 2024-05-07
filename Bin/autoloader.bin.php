<?php

defined('ROOTPATH') and defined('DS') or die('something wrong');


class ClassNotFoundException extends Exception { }


spl_autoload_register(function ($class_name) {

	$found = false;

	$folders = array(
		'Includes' 	 							=> '.inc.',
		'Classes' 	 							=> '.class.',
		'Bin'											=> '.bin.',
		'viewControllers'					=> '.contrl.',
		'Classes'.DS.'Protectors'	=> '.class.',
		'Classes'.DS.'Priveleges'	=> '.class.',
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

			throw new ClassNotFoundException(sprintf('Сlass %s not found!', $class_name), 1);
		}

	} catch (ClassNotFoundException $e) {
		//die(Debugger\Debug::debugger($e->getMessage(), __FUNCTION__));
	}
});
