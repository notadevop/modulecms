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
date_default_timezone_set('UTC');

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

$files = array(

	'init.inc.php',
	'config.inc.php',
	'settings.inc.php',
	'extended.inc.php',
	'Debugger.inc.php',

	'meta/Attentions.meta.php',
	'meta/Info.meta.php',
	'meta/Errors.meta.php'
);

foreach ($files as $key => $value) {
	$r = ROOTPATH . $value;

	!file_exists($r) ? die('no file!'. $r) : require_once ($r);
}

/*
	Класс разделения контента на страницы Pagination (break to pages)
	Класс манипуляции с Даты 
	Класс Отправки емайлов
	Метод хуков????
	url_fixer разрешает конфликты в ссылках
*/


$v = new vRender();

$v->prepareRender();

Router::initDefaultRoutes();

$result = Router::getResult();

$viewRender = new ViewRender();
$viewRender->setActiveTemplate('simplelight');
//$viewRender->setActiveTemplate('bootstrap');
$viewRender->prepareRender($result);


$timer = function() use ($start){
	$time 		= microtime();
	$time 		= explode(' ', $time);
	$time 		= $time[1] + $time[0]; 
	$finish 	= $time;
	$total_time = round(($finish - $start), 4);
	return 'Загрузка: ' . $total_time . ' cекунд.';
};

$viewRender->replace(
	array(
		'/%memused%/i'		=> 'Использованная память: '. convert(memory_get_usage(true)),
		'/%username%/i' 	=> PROFILE['username'],
		'/%loadtime%/i' 	=> $timer(), 
	)
);
// в конечном итоге вывидим все.
$viewRender->viewRender();

// Конец Исполнения кода 












