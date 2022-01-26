<?php 


/**
 *  	Класс нужен для единой точки обработки и получения глобальных переменных 
 */
class GlobalParams {
	
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
			case '_GET':		$this->gp = $_GET;		break;
			case '_COOKIE':		$this->gp = $_COOKIE; 	break;
			case '_SESSION': 	$this->gp = $_SESSION; 	break;
			case '_SERVER': 	$this->gp = $_SERVER; 	break;
			default: 			$this->gp = null;		break;
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


