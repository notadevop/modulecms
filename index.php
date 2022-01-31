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
ini_set('error_reporting', 'E_ALL');
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: ModuleCMS');

define('DS', DIRECTORY_SEPARATOR);
define('ROOTPATH', dirname(__FILE__) . DS);


$files = array(

	'init.inc.php',
	'config.inc.php',
	'settings.inc.php',
	'extended.inc.php',
	'Debugger.inc.php',

	'meta/Attentions.meta.php',
	'meta/Info.meta.php',
	'meta/Errors.meta.php',
	'meta/Staticlinks.meta.php',
);

foreach ($files as $key => $value) {
	$r = ROOTPATH . $value;
	!file_exists($r) ? die('Не могу найти системный файл!') : require_once ($r);
}

$v = new vRender();
$v->prepareRender();

$timer = function() use ($start){
	$time 		= microtime();
	$time 		= explode(' ', $time);
	$time 		= $time[1] + $time[0]; 
	$finish 	= $time;
	$total_time = round(($finish - $start), 4);
	return 'Загрузка: ' . $total_time . ' cекунд.';
};

$v->replace(
	array(
		'%memused%'		=> 'Использованная память: '.convert(memory_get_usage(true)),
		'%loadtime%' 	=> $timer(), 
	)
);

$v->viewRender();


