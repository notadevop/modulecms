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


 // Для определения метода или функции __METHOD__, __FUNCTION__

function debugger($input, $param=__FUNCTION__, $vardump=false): void {

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
			<hr/>
			<p>Файл откуда запущен: => <?=basename( $_SERVER['PHP_SELF'] ); ?></p>
			<p>Путь Исполнения: => <b style="color: red;"> <?=$param; ?></b></p>	
			<p>Результат Исполнения: => 
				<?php 
				echo '<pre>';
				print_r($input); 
				echo '</pre>';
				?>
				</p>
			<?php // var_dump($input); // ?>
	</h4>
	<?php
}

// 
function console($input, $param='', $doAction) { }


