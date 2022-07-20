<?php 

/**
 *		Класс для работы с POST посылками 
 * 		для того, чтобы унифицировать работу с POST данными 
 * 		используется этот класс 
 * 
 */
class PostAgregator {

	private static $savedData = array();

	// Сохраняем все переменные _POST 

	public static function initInputData():void {

		self::$savedData = $_POST; // [0]

		unset($_POST);
	}

	// по ключам массива вытаскиваем нужные переменные 

	public static function getPostData(array $keys): ?array{

		return !empty($keys) ? array_intersect(self::$savedData, $keys) : array();
	}

	function delegator($clouser) {

		// тут даем исполнение для замыкания внешнего кода который можно было бы использовать 
		// с сохраненными данными из _POST данных

	}
}