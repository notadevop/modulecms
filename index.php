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


$routes = array(
	'/'						=> 'UserIdentificator/testing/helloworld',
	'/auth/login' 			=> 'UserIdentificator/loginAction',
	'/auth/register' 		=> 'UserIdentificator/registerAction',
	'/auth/restore' 		=> 'UserIdentificator/restoreAction',
	'/auth/confirmRestore' 	=> 'UserIdentificator/confirmRestoreAction',
	'/auth/logout' 			=> 'UserIdentificator/logout/true/false',
);






Routing::addRoute($routes); // Добавляем пути
//Routing::addRoute('/about', 'MainController/about'); // Добавляем один путь
$result = Routing::dispatch('/');  
$result = Routing::dispatch(); 

debugger($result,__FUNCTION__);


// Отправить в pageBuilder

function loadTemplate(string $template, string $file,array $metadata=array()){

	require_once ( ROOTPATH . 'Templates'.DS.$template.DS.$file.'.tpl.php');
}

loadTemplate('default', 'header');

$curUri = Routing::getCurrentUri();

if (preg_match('#^'.$curUri.'$#', '/auth/login')) {

	loadTemplate('default', 'login'); 

} else if (preg_match('#^'.$curUri.'$#', '/auth/register')) {

	loadTemplate('default', 'register'); 

} else if (preg_match('#^'.$curUri.'$#', '/auth/restore')) {

	loadTemplate('default', 'restore'); 

} else if (preg_match('#^'.$curUri.'$#', '/auth/confirmrestore')) {

	loadTemplate('default', 'pwdReset');

} else {

	loadTemplate('default', 'infopage'); 
}

loadTemplate('default', 'footer');


$time 	= microtime();
$time 	= explode(' ', $time);
$time 	= $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Страница сгенерированна через: '.$total_time.' cекунд.';