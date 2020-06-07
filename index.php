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
require_once ROOTPATH . 'Includes/Routes.inc.php';

$uriRoutes = array();
$permResult = array(); // Тут мы сохраняем результат того, что приходит из метода контроллера

foreach ($routes as $key => $value) {

	$actUri = array($key => $value['action']); 

	if ($value['skipUri']) {
		Routing::addRoute($actUri);
		$permResult[$key] = Routing::dispatch($key);
		Routing::cleanRoutes($key);
	} else {
		$uriRoutes[$key] = $value['action'];
	}
}


Routing::addRoute($uriRoutes); // Добавляем пути
$tplRes = Routing::dispatch();


// TODO: 123 <== Временно, написать класс ViewRender.class.php => pageBuilder.ctrl.php

function loadTemplate($params, $permRes, $tplRes) {

	$route = Routing::getRoutes()[0];
	$route = empty($route) ? '/' : '/' . $route;

	if ( !isset($params[$route]) ) {

		$route = '/';
	}

	$defTpl 	= $params[$route]['template'];
	$ifRegOk 	= $params[$route]['ifRegOk'];

	$renderTpl = (defined('PROFILE') && !empty(PROFILE['useremail'])) ? $ifRegOk : $defTpl;


	// TODO: перенести в класс рендеринга и там загружать настройки шаблона и по нему выводить страницы 

	require_once TPLDEFAULTFOLDER . TPLDEFAULTTEMPLATE . 'header.tpl.php';
	require_once TPLDEFAULTFOLDER . TPLDEFAULTTEMPLATE . $renderTpl;
	require_once TPLDEFAULTFOLDER . TPLDEFAULTTEMPLATE . 'footer.tpl.php';
}

loadTemplate($routes, $permResult, $tplRes);


$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Страница сгенерированна через: ' . $total_time . ' cекунд.';