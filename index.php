<?php

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

if (version_compare(phpversion(), '8.0.0', '>=') == true) {

	die('PHP 7.0 or newer Only!');
}

if (session_id() == '') {session_start();}

header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: PHP Application');
ini_set('error_reporting', E_ALL);
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


$result = array(
			'permContrResult' 	=> array(), 
			'templContrResult' 	=> array()
		);

$routes = Routing::initDefRoutes();

//
foreach ($routes as $key => $value) {
	
	Routing::addRoute($key, $value['action']);

	// Условие нужно для перманентных контроллеров
	// которые не используют пути и должны испольнятся всегда
	// После запуска удаляем путь 

	if ($value['skipUri']) { 

		$result['permContrResult'][$key] = Routing::dispatch($key);
		Routing::cleanRoutes($key);
	}
}

 // Не показывает пути ПОчему ???
//debugger(Routing::getNameOfRoute(),'imja route');

$result['templContrResult'] = Routing::dispatch();

debugger(Routing::getNameOfRoute(),'imja route');



$viewRender = new ViewRender();
$viewRender->setActiveTemplate('default');
$viewRender->genPrevMap($routes, $result, Routing::getNameOfRoute());


$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Страница сгенерированна через: ' . $total_time . ' cекунд.';