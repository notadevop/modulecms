<?php 


/**
 *    Класс по умолчанию, пока ничего не делает!! 
 */


class MainController {
	
	function __construct() { }

	function test($param) {
		
		Logger::collectAlert('primary', 'TEST метод');
	}

	function defaultMethod() {

		Logger::collectAlert('primary', 'DEFAULT метод');
	}
}