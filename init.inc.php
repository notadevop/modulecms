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
	<!--
	<h2>The vertical-align Property</h2>
	<p></p>
	<table>
	  <tr>
	    <th>Firstname</th>
	    <th>Lastname</th>
	  </tr>
	  <tr>
	    <td>Peter</td>
	    <td>Griffin</td>
	  </tr>
	</table>
	-->
	<h4>
		<?php 
			echo 'Определенные пользователем функции:';

			$functions = get_defined_functions();
			$r = array_keys($functions['user']);

			echo '<pre>';
			print_r($r);
			echo '</pre>';
		?>

		<!--<p>Файл запущен: => <?=basename( $_SERVER['PHP_SELF'] ); ?></p>-->
		<p>Путь Исполнения: => <b style="color: red;"> <?=$param; ?></b></p>	
		<p>Результат Исполнения: => <pre><?php print_r($input); ?></pre></p><hr/>
		<?php // var_dump($input); // ?>
	</h4>

	<?php
	if ($debug) {
		ob_start();
		echo '<pre>';
		//debug_print_backtrace(); // выводи стек, как вариант: debug_backtrace();
		print_r(debug_backtrace());
		echo '</pre>';
		$trace = ob_get_contents();
		ob_end_clean();
		echo '<h1> Backtrace: </h1><hr /><pre>';
		print_r(genCallTrace());
		//debug_print_backtrace();
		//print_r($trace); // debug_backtrace;
		echo '</pre>';
	}
}

// 
function console($input, $param='', $doAction) { }


