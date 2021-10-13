<?php 


/**
 * 
 */
class MainController {
	
	function __construct() { }

	function test($param) {


		Logger::collectAlert('primary', 'Пустой метод');

		return 'это параметр который указан в URI =>'.$param;
	}

	function defaultMethod() {

		Logger::collectAlert('primary', 'Пустой метод');

		return array('Метод по умолчанию', 'Тут никаких действий!');
	}
}