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
			
			// delete???
			$path = str_replace('\\', DIRECTORY_SEPARATOR, $path);

			if (file_exists($path)) {
				$flag = true; 
				break;
			} 
		}
		
		if (!$flag) {
			throw new Exception('Класс => <b style="color: red;">'. $class_name .'</b> не найден. Невозможно продолжить, исправь это!');
		}

		require_once ( $path );	
	
	} catch (Exception $e) {

		die(debugger($e->getMessage(), 'spl_autoload_register'));
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


 // Для определения метода или функции __METHOD__, __FUNCTION__

function debugger($input, $param=__FUNCTION__, $debug=false): void {

	?>

	<div style="margin: 15px; padding: 10px">
	<p>Файл запущен из: <?=basename( $_SERVER['PHP_SELF'] ); ?></p>
		<p>Объект или доп. информация: <b style="color: red;"> <?=$param; ?></b></p>	
		<p>Исполнитель: 
	<?php 

	if (empty($input)) {

		echo 'переменная пустая или не указанна: <br/>';
		var_dump($input);

	} else {
		?><br /><pre style='margin: 5px'><?php print_r($input); ?></pre></p><hr/><?php	
	}

	echo '</div>';
	return;
	?>

	<h4>
		<?php 
			echo 'Определенные пользователем функции:';

			$functions = get_defined_functions();
			$r = array_keys($functions['user']);

			echo '<pre>';
			print_r($r);
			echo '</pre>';
		?>


	</h4>
	<?php
}

// 
function console($input, $param='', $doAction) { }


