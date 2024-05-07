<?php
declare(strict_types=1);

namespace {

	$minimumPHPVersion='7.0.0';

	if (version_compare(phpversion(), $minimumPHPVersion) <= 0) {
		die('PHP '.$minimumPHPVersion.' or newer Only! Your version PHP is: '.phpversion());
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
	define('DEBUG', true);


	require_once (ROOTPATH.'Bin'.DS.'Debugger.bin.php');
	require_once (ROOTPATH.'Bin'.DS.'autoloader.bin.php');
	require_once (ROOTPATH.'Classes'.DS.'Logger.class.php');
	require_once (ROOTPATH.'Classes'.DS.'Languages.class.php');
	require_once (ROOTPATH.'Classes'.DS.'Database.class.php');

	require_once (ROOTPATH.'viewControllers'.DS.'SettingsLoader.controller.php');

	require_once (ROOTPATH.'MainFile.exec.php');


	$mainExec = new MainFile\MainExecutor();

	$mainExec->initCoreSettings();


	$mainExec->showView();



	// Определить разные типа аттак
	//  CSRF
	//  XSS
	//  SQL-INJECTION
	//  BRUTEFORCE
	//  PHP/JS CODE INJECTION


}
