<?php
declare(strict_types=1);
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;



if (version_compare(phpversion(), '7.0.0') <= 0) {
	
	die('PHP 7.0 or newer Only! Your version PHP is: '.phpversion());
}

if (session_id() == '') { session_start(); }

setlocale(LC_CTYPE, 'en_US.UTF-8');

header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: PHP Application');
ini_set('error_reporting', 'E_ALL');
error_reporting(E_ALL);

//const 'DS' = DIRECTORY_SEPARATOR;
//const 'ROOTPATH' = dirname(__FILE__) . DS;

define('DS', DIRECTORY_SEPARATOR);
define('ROOTPATH', dirname(__FILE__) . DS);

// header( "refresh:1; url=index.php" );
// Указываем все необходимые файлы для загрузки


define('DEBUG', true);

foreach (

	array(
		'settings.inc.php',
		'config.inc.php',
		'init.inc.php',
		'extended.func.php'
	) 
	as $key => $value) {
	
	require_once ROOTPATH . $value;
}


/*
	Класс разделения контента на страницы Pagination (break to pages)
	Класс манипуляции с Даты 
	Класс Отправки емайлов
	Метод хуков????
	url_fixer разрешает конфликты в ссылках
*/

$host = new HostSettings();

Router::initDefaultRoutes();

$result = Router::getResult();

$viewRender = new ViewRender();

$viewRender->setActiveTemplate('simplelight');
$viewRender->prepareRender($result);



$timer = function() use ($start){
	$time 		= microtime();
	$time 		= explode(' ', $time);
	$time 		= $time[1] + $time[0]; 
	$finish 	= $time;
	$total_time = round(($finish - $start), 4);
	return 'Загрузка: ' . $total_time . ' cекунд.';
};


Router::getRoute();


$viewRender->replace(
	array(
		'/%loadtime%/i' 	=> $timer(),
		'/%username%/i' 	=> PROFILE['username'], 
		'/%memused%/i'		=> 'Использованная память: '. convert(memory_get_usage(true))
	)
);
// в конечном итоге вывидим все.
$viewRender->viewRender();

// Конец Исполнения кода 












