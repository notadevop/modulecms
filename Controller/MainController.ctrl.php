<?php 


/**
 * 
 */
class MainController {
	
	function __construct() { }

	function test($param) {

		return 'это параметр который указан в URI =>'.$param;
	}

	function defaultMethod() {

		return array('Метод по умолчанию', 'Тут никаких действий!');
	}
}