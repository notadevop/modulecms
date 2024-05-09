<?php

namespace Globals;

enum GlobalIndexes {

	case POST;
	case GET;
	case FILES;
	case COOKIE;
	case SESSION;
	case SERVER;
}

final class GlobalParams {

	private static $post 		= Array();
	private static $get 		= Array();
	private static $cookie 	= Array();
	private static $session = Array();

	static function initGlobalParams() {

			if(isset($_POST)) {
				self::$post = $_POST;
				$_POST = Array();
			}
			if(isset($_GET)) {
				self::$get = $_GET;
				$_GET = Array();
			}
			if(isset($_COOKIE)) {
				self::$cookie = $_COOKIE;
				$_COOKIE = Array();
			}
			if(isset($_SESSION)) {
				self::$session = $_SESSION;
				$_SESSION = Array();
			}
	}

 	static function getGlobalParams(String $globalName) {

		$returnArray = Array();

			switch($globalName) {
				case 'POST': 		$returnArray = self::$post; 	break;
				case 'GET': 		$returnArray = self::$get; 		break;
				case 'COOKIE':	$returnArray = self::$cookie; break;
				case 'SESSION':	$returnArray = self::$session;break;

				default: 				$returnArray = Array(); 			break;
			}

			return count($returnArray) < 1 ? Array() : $returnArray;
	}

}




/*
return;
class GlobalParamsss {

	function __construct($param='') {

		if (!empty($param)) {
			$this->setGlobParam($param);
		}
	}

	private $gp = null;

	public function setGlobParam(string $param=''): void {

		if(empty($param)) {
			$param = $_SERVER['REQUEST_METHOD'];
		}

		switch ($param) {
			case '_POST': 		$this->gp = $_POST;		break;
			case '_FILES': 		$this->gp = $_FILES; 	break;
			case '_GET':			$this->gp = $_GET;		break;
			case '_COOKIE':		$this->gp = $_COOKIE; 	break;
			case '_SESSION': 	$this->gp = $_SESSION; 	break;
			case '_SERVER': 	$this->gp = $_SERVER; 	break;
			default: 					$this->gp = null;		break;
		}
	}

	function getBulkParams(array $paramKeys=array()): ?array {

		if(empty($paramKeys)) {return null;}

		return array_intersect_assoc($paramKeys, $this->gb);
	}


	// Проверяем существует ли параметр

	public function isExist(string $key): bool {

		return (!isset($this->gp) || !array_key_exists($key, $this->gp)) ? false : true;
	}

	// получаем

	public function getGlobParam(string $key): ?string {

		return $this->isExist($key) ? $this->gp[$key] : null;
	}
}
*/
