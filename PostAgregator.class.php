<?php 

/**
 *		Класс для работы с POST посылками 
 * 		для того, чтобы унифицировать работу с POST данными 
 * 		используется этот класс 
 * 
 */
class PostAgregator {

	private static $savedData = array();

	public static function initilizePostData():void {

		self::$savedData = $_POST;

		unset($_POST);
	}

	public static function getPostData($key, ) {



	}

}