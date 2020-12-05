<?php
declare(strict_types=1);
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

if (version_compare(phpversion(), '8.0.0', '>=') == true) {
	
	die('PHP 7.0 or newer Only!');
}

if (session_id() == '') { session_start(); }

header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: PHP Application');
ini_set('error_reporting', 'E_ALL');
error_reporting(E_ALL);

define('DS', DIRECTORY_SEPARATOR);
define('ROOTPATH', dirname(__FILE__) . DS);


// header( "refresh:1; url=index.php" );

// Загрузка файлов без классов
require_once ROOTPATH . 'settings.inc.php';
require_once ROOTPATH . 'config.inc.php';
require_once ROOTPATH . 'init.inc.php';

/*
	Класс разделения контента на страницы Pagination (break to pages)
	Класс манипуляции с Даты 
	Класс Отправки емайлов
	Метод хуков????
	url_fixer разрешает конфликты в ссылках
*/

$host = new HostSettings();



Router::initDefaultRoutes();
$routes 	= Router::getSavedRoutes();
$result 	= Router::getResult();
$curRoute 	= Router::getCurrentRouteParams();

$viewRender = new ViewRender($curRoute);

$viewRender->setActiveTemplate('simplelight');
$viewRender->prepareRender($routes, $result, Routing::getNameOfRoute());

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
$load = 'Загрузка: ' . $total_time . ' cекунд.';

function convert($size) {
    $unit=array('b','Kb','Mb','Gb','Tb','Pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

$mem = 'Занятая память: '.convert(memory_get_usage(true)); // 123 kb



$viewRender->replace(
	array(

		'%loadtime%' 	=> $load,
		'%username%' 	=> PROFILE['username'], 
		'%memused%'		=> $mem
	)
);
// в конечном итоге вывидим все.
$viewRender->viewRender();



