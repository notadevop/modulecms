<?php 

/**
 * 
 */
class Errors {
	
	function __construct() { 

		$this->errors = array();
	} 

	private $errors;

	// Собираем все ошибки
	public function collectErrors(string $key, string $error): void {

		$this->errors[$key] = $error;
	}

	// Нужен для изменения выводимых ошибок 
	public function errorKeyExist(string $key): bool {

		return array_key_exists($key, $this->errors);
	}

	// Возвращаем все собранные ошибки
	public function getErrors() {

		return $this->errors;
	}
}