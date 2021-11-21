<?php 


/**
 *    Класс по умолчанию, пока ничего не делает!! 
 */


class MainController {

	private $glob;

	function __construct() {

		$this->glob 	= new GlobalParams();
	}
	
	function defaultMethod() {

		Logger::collectAlert('primary', 'DEFAULT метод');
	}

	/**
	 *	Метод который вылавливает информационные баннеры из 
	 * 	глобальных переменных _GET, _POST, _COOKIE.
	 */

	function catchInfoDaemon(): void {

		$this->glob->setGlobParam('_GET');



		//Logger::collectAlert('primary', 'test 1');
		//Logger::collectAlert('primary', 'test 2');
	}
}