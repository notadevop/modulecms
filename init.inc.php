<?php 

(defined('ROOTPATH') && defined('DS')) or die('something wrong');

class ClassNotFoundException extends Exception { }

// Обьявить пространство имен для защиты от запуска из любого класа из любой точки

spl_autoload_register(function ($class_name) {

	// TODO: Использовать замыкание для отловки ошибки

	$flag = false;

	// Указанна папка => префикс файла

	$folders = array(
		'Includes' 	 	=> '.inc.',
		'Classes' 	 	=> '.class.',
		'Controller' 	=> '.ctrl.',   // Удалить поже!!!
		'bin'			=> '.bin.',
		'viewController'=> '.contr.'
	);

	try {
		foreach ($folders as $key => $value) {

			$path = ROOTPATH.$key.DS.$class_name.$value.'php'; 
			
			// delete???
			$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);

			if (file_exists($path)) {
				$flag = true; 
				break;
			} 
		}
		
		if (!$flag) {
			throw new ClassNotFoundException('Класс <b>'. $class_name .'</b> не найден!', 1);
		}

		require_once ( $path );	
	
	} catch (ClassNotFoundException $e) {

		die(debugger($e->getMessage(), __FUNCTION__));
	}
}); 


function genCallTrace(){

    $e = new Exception();
    $trace = explode("\n", $e->getTraceAsString());
    // reverse array to make steps line up chronologically
    $trace = array_reverse($trace);
    array_shift($trace); // remove {main}
    array_pop($trace); // remove call to this method
    $length = count($trace);
    $result = array();
   
    for ($i = 0; $i < $length; $i++) {

        $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
    }
   
    return "\t" . implode("\n\t", $result);
}





function debugger($input='emptyOutput', $category=DEBUG, $params=array()): void {

	// Для массива  array $params использовать ниже указанные параметры: 

	/*
	__FILE__ – The full path and filename of the file.
	__DIR__ – The directory of the file.
	__FUNCTION__ – The function name.
	__CLASS__ – The class name.
	__METHOD__ – The class method name.
	__LINE__ – The current line number of the file.
	__NAMESPACE__ – The name of the current namespace
	*/

	//$category = DEBUG;
	
	$preb = '<h3><pre style="margin: 45px; padding: 40px; color: blue;">';
	$pree = '</pre></h3>';

	echo $preb;	

	$userfunc = !true;

	if ($category) {
		$debug = debug_backtrace();
		//debug_print_backtrace();
		if (!empty($debug)){
			for ($i=0; $i < count($debug); $i++) { 
				if($debug[$i]['function'] == 'debugger' ) {
					echo 'Debug Line: '. $debug[$i]['line'].'<br/>';
					echo 'File: '.$debug[$i]['file'].'<br/>';
					echo '<span style="color: red;">Variable OUTPUT: </span><br/>';
					print_r($debug[$i]['args']);
				} 
			}
			
			if($userfunc) {
				echo '<span style="color: red;">Пользовательские функции: </span><br/>';
				$functions = get_defined_functions();
				$r = array_keys($functions['user']);
				print_r(array_values($functions['user']));
			}

		} else { echo 'debug is empty!'; }
		
	} else { print_r($input); }

	echo $pree;
	//echo '<hr/ style="color: black;">';

	return;
}
