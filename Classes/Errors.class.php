<?php 

/**
 * 
 */
class Errors {
	
	function __construct() { 

		$this->errors 			= array();
		$this->notifications 	= array();
	} 

	private $errors;
	private $notifications;

	// ----------- Напоминания, предупреждения и т.д.

	public function collectNotif(string $key, string $notif): void {

		$this->notifications[$key] = $notif;
	}

	public function notifKeyExist(string $key, string $notif): bool {

		return array_key_exists($key, $this->notifications);
	}

	public function getNotif(): ?array{

		return $this->notifications;
	}

	// ----------- Ошибки и т.д.

	public function collectErrors(string $key, string $error): void {

		$this->errors[$key] = $error;
	}

	public function errorKeyExist(string $key): bool {

		return array_key_exists($key, $this->errors);
	}

	public function getErrors(): ?array{

		return $this->errors;
	}
}