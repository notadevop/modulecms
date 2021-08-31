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

	private $gp; 

	public function setGlobParam(string $param='na'): void {

		switch ($param) {
			case '_POST': 		$this->gp = $_POST;		break;  
			case '_FILES': 		$this->gp = $_FILES; 	break;
			case '_GET':		$this->gp = $_GET;		break;
			case '_COOKIE':		$this->gp = $_COOKIE; 	break;
			case '_SESSION': 	$this->gp = $_SESSION; 	break;
			default: 			$this->gp = null;		break;
		}
	}

	// Проверяем существует ли параметр

	public function isExist(string $key): bool {

		if(empty($this->gp)) { return false; } 

		return array_key_exists($key, $this->gp);
	}

	// получаем 

	public function getGlobParam(string $key): ?string {

		/*
		if (!array_key_exists($key, $this->globtype)) {

			throw new Exception('Такого параметра не существует!', 1);

		} else return $this->globtype[$key];
		*/

		return $this->isExist($key) ? $this->gp[$key] : null;
	}
}


