<?php 

$time 	= microtime();
$time 	= explode(' ', $time);
$time 	= $time[1] + $time[0];
$start 	= $time;

if (version_compare(phpversion(), '8.0.0', '>=') == true) { die ('PHP 7.0 or newer Only!'); }

if (session_id() == '') { session_start(); }

header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: PHP Application');
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);

define('DS', DIRECTORY_SEPARATOR);
define('ROOTPATH', dirname(__FILE__).DS); 

// header( "refresh:1; url=index.php" ); 

require_once ( ROOTPATH . 'config.inc.php');

/* Автозагрузчик всех классов и инклюдов */
require_once ( ROOTPATH . 'init.inc.php');

// Временно
require_once ( ROOTPATH . 'Controller/Userid.controller.php');

$id = new UserIdentificatior();
$id->__init_auth();


// Временно для дебага 
require_once ( ROOTPATH . 'Templates/default/header.tpl.php');

switch(@$_GET['action']) {
	case'login': 	require_once ( ROOTPATH . 'Templates/default/login.tpl.php'); 		break;
	case'register': require_once ( ROOTPATH . 'Templates/default/register.tpl.php'); 	break;
	case'restore': 	require_once ( ROOTPATH . 'Templates/default/restore.tpl.php'); 	break;
	case'pwd': 		require_once ( ROOTPATH . 'Templates/default/pwdReset.tpl.php'); 	break;
	default: 		debugger('Не указано!'); break;
}
require_once ( ROOTPATH . 'Templates/default/footer.tpl.php');




$time 	= microtime();
$time 	= explode(' ', $time);
$time 	= $time[1] + $time[0];
$finish = $time;
$total_time = round(($finish - $start), 4);
echo 'Page generated in '.$total_time.' seconds.';

