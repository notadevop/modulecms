<?php 


/**
 *  	Класс нужен для единой точки обработки и получения глобальных переменных 
 */
class GlobalParams {
	
	function __construct($param='') { 

		if (!empty($param)) { $this->setGlobParam($param); }
	}

	private $globtype; 

	public function setGlobParam(string $param='na'): void {

		switch ($param) {
			case '_POST': 		$this->globtype = $_POST;		break;  
			case '_FILES': 		$this->globtype = $_FILES; 		break;
			case '_GET':		$this->globtype = $_GET;		break;
			case '_COOKIE':		$this->globtype = $_COOKIE; 	break;
			case '_SESSION': 	$this->globtype = $_SESSION; 	break;
			default: 			$this->globtype = null;			break;
		}
	}

	// Проверяем существует ли параметр

	public function isExist(string $key): bool {

		if(empty($this->globtype)) { return false; } 

		return array_key_exists($key, $this->globtype);
	}

	// получаем 

	public function getGlobParam(string $key): ?string {

		return $this->isExist($key) ? $this->globtype[$key] : null;
	}
}


