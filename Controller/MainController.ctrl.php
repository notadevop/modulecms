<?php 


/**
 * 
 */
class MainController extends Errors{
	
	function __construct() {

		parent::__construct();
	}

	function defaultMethod() {

		debugger('Незнаю, что открывать!', __METHOD__);
	}
}