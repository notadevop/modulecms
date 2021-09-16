<?php 

class Blocker {

	// Определитель CSRF проверяем существует и соответсвтует уникальный ключ

	function verifcsrf(string $id=''): bool {

		if (!$id || empty($id) || session_id() == '') { return false; }

		return false;
	}

	// Получаем уникальный ключ для формы

	function getFormUniqId(): string {

		return '';
	}

	function setGlobalAsId($param='') {

		switch($param) {
			case'_GET':
			
			break;
			case'_POST':
			
			break;
			case'_COOKIE':
			
			break;
			default:
			
			break;
		} 
	} 


	// Определяем брутфорс 

	function isbrutef(string $id): bool {

		return false;
	}
}