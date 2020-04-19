<?php 

$time 	= microtime();
$time 	= explode(' ', $time);
$time 	= $time[1] + $time[0];
$start 	= $time;

if (version_compare(phpversion(), '8.0.0', '>=') == true) { 

	die ('PHP 7.0 or newer Only!'); 
}

if (session_id() == '') { session_start(); }

header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: PHP Application');
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);

define('DS', DIRECTORY_SEPARATOR);
define('ROOTPATH', dirname(__FILE__).DS); 

// header( "refresh:1; url=index.php" ); 


// Загрузка файлов без классов
require_once ( ROOTPATH . 'settings.inc.php');
require_once ( ROOTPATH . 'config.inc.php');
require_once ( ROOTPATH . 'init.inc.php');


// --- Перманентные пути нужны для исполнения на любой странице

$permRoutes = array(

	'/authAction'  	=> 'UserIdentificator/authAction',
	'/online'		=> 'Visitor/users_online'
);

$res = array();

Routing::addRoute($permRoutes);

foreach ($permRoutes as $key => $value) {
	
	$res[$key] = Routing::dispatch($key);
	Routing::cleanRoutes($key);
}


echo 'Кол-во пользователей на сайте: '.$res['/online']['ctrlres'];


// ----------------

$routes = array(
	'/'						=> 'UserIdentificator/testing/helloworld',
	'/usersonline'			=> 'Visitor/getOnlineUsers',
	'/login' 				=> 'UserIdentificator/loginAction',
	'/register' 			=> 'UserIdentificator/registerAction',
	'/restore' 				=> 'UserIdentificator/restoreAction',
	'/confirmrestore' 		=> 'UserIdentificator/confirmRestoreAction',
	'/confirmregistration' 	=> 'UserIdentificator/confirmRegistrationAction',
	'/logout' 				=> 'UserIdentificator/logout/true/false',
);

Routing::addRoute($routes); // Добавляем пути
$result = Routing::dispatch(); 


// Временное что-то типа шаблонизатора 
$templates = array(

	'/'						=> 'infopage.tpl.php',
	'/login'				=> 'login.tpl.php',
	'/usersonline'			=> 'infopage.tpl.php',
	'/restore'				=> 'restore.tpl.php',
	'/confirmrestore'		=> 'passform.tpl.php',
	'/register'				=> 'register.tpl.php',
	'/confirmregistration'	=> 'infopage.tpl.php',
	'/logout'				=> 'infopage.tpl.php',
);

function loadTemplate($metadata='', $templates) {

	require_once (TPLDEFAULTFOLDER.TPLDEFAULTTEMPLATE.'header.tpl.php');

	$curUri = '/'.Routing::getRoutes()[0];

	if(empty($curUri)) $curUri = '/';

	$notemplate = true;

	foreach ($templates as $key => $value) {
		
		if (preg_match('#^'.$curUri.'$#', $key)) {

			require_once (TPLDEFAULTFOLDER.TPLDEFAULTTEMPLATE.$value);
			$notemplate =false;
		}
	}

	if($notemplate) {

		require_once (TPLDEFAULTFOLDER.TPLDEFAULTTEMPLATE.$templates['/']);
	}

	require_once ( TPLDEFAULTFOLDER.TPLDEFAULTTEMPLATE.'footer.tpl.php');
}

loadTemplate($result, $templates);




$time 	= microtime();
$time 	= explode(' ', $time);
$time 	= $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Страница сгенерированна через: '.$total_time.' cекунд.';