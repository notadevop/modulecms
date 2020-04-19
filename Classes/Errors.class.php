<?php 

/**
 * 
 */
class Errors {
	
	function __construct() { 

		$this->errors = array();
	} 

	private $errors;

	public function collectErrors(string $key, string $error): void {

		$this->errors[$key] = $error;
	}

	public function getErrors() {

		return $this->errors;
	}
}