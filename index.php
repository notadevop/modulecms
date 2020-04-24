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


// Код который нужно запустить независимо от пути
$execRoutes = array(

	'auth' 			=> 'UserIdentificator/authAction',
	'online' 		=> 'Visitor/users_online',
);

// Код который запускается в зависимости от пути
$pathRoutes = array(
	'/' 			=> 'MainController/defaultMethod',
	'/posts/:any' 	=> 'MainController/test/$1', // Использовать для постов
	'/usersonline' 	=> 'Visitor/getOnlineUsers',
	'/login' 		=> 'UserIdentificator/loginAction',
	'/register' 	=> 'UserIdentificator/regAction',
	'/restore' 		=> 'UserIdentificator/resAction',
	'/verifres' 	=> 'UserIdentificator/verifyUserModifications',
	'/confpass'		=> 'UserIdentificator/updatePassword',
	'/verifreg' 	=> 'UserIdentificator/verifyRegistration',
	'/logout' 		=> 'UserIdentificator/logout/true/false',


	'/profile' 		=> 'ProfileController/getUserProfile',
);


$res = array(); // Собираем все данные с контроллеров которые запущенны перманентно!

Routing::addRoute($execRoutes);

foreach ($execRoutes as $key => $value) {

	$res[$key] = Routing::dispatch($key);
	Routing::cleanRoutes($key);
}

Routing::addRoute($pathRoutes); // Добавляем пути
$result = Routing::dispatch();

// Временное что-то типа шаблонизатора
$templates = array(

	'/' 			=> 'infopage.tpl.php',
	'/login' 		=> 'login.tpl.php', 
	'/usersonline' 	=> 'infopage.tpl.php',
	'/restore' 		=> 'restore.tpl.php',
	'/verifres' 	=> ($result['result'] ? 'passform.tpl.php' : 'infopage.tpl.php'),
	'/register' 	=> 'register.tpl.php',
	'/verifreg' 	=> 'infopage.tpl.php',
	'/logout' 		=> 'infopage.tpl.php',
);

function loadTemplate($metadata = '', $templates, $res) {

	$curUri = Routing::getRoutes()[0];

	if (empty($curUri)) { $curUri = '/'; } else { $curUri = '/'.$curUri; }

	$notemplate = false;

	foreach ($templates as $key => $value) {

		if (preg_match('#^' . $curUri . '$#', $key)) {
			$notemplate = $value;
		} 
	}

	if (!$notemplate) {
		$notemplate = $templates['/'];
	}

	require_once TPLDEFAULTFOLDER . TPLDEFAULTTEMPLATE . 'header.tpl.php';
	require_once TPLDEFAULTFOLDER . TPLDEFAULTTEMPLATE . $notemplate;
	require_once TPLDEFAULTFOLDER . TPLDEFAULTTEMPLATE . 'footer.tpl.php';
}

loadTemplate($result, $templates, $res);


$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Страница сгенерированна через: ' . $total_time . ' cекунд.';