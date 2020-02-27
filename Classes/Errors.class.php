<?php 

/**
 * 
 */
class Errors {
	
	function __construct() { 

		$this->errors = array();
	} 

	private $errors;

	function initErrors(): Errors {

		return new Errors();
	}

	public function collectErrors(string $key, string $error): void {

		$this->errors[$key] = $error;
	}

	public function getErrors(): array {

		return $this->errors;
	}
}