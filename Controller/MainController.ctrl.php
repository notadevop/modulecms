<?php 


/**
 * 
 */
class MainController extends Errors{
	
	function __construct() {

		parent::__construct();
	}

	function test($param) {


		return 'это параметр который указан в URI =>'.$param;
	}

	function defaultMethod() {

		Logger::collectAlert('success', 'Метод по умолчанию');

		return array('Метод по умолчанию', 'Тут никаких действий!');
	}
}