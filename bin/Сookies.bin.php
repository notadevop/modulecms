<?php 

/*

	Класс для работы с куки 
	cохраниние, поиск, удаление 
*/



final class Cookies {
	
	private $cookies;

	function __construct() {

		$this->cookies = array();
	}

	// Проверяем существует ли параметр в массиве 

	function cookieKeyExist(string $cookie_name=''): bool {

		if (!isset($cookie_name) || empty($cookie_name)) { return false; }

		return array_key_exists($cookie_name, $this->cookies) ? true : false;
	}

	// иницилизируем куки

	function initCookie(string $cookie_name=''): void {

		if ($this->cookieKeyExist($cookie_name)) {return;}

		$this->cookies[$cookie_name] = array(

			'hostPath' 		=> '',
			'hostDomen'		=> '',
			'cookieTime'	=> ''
		);
	}

	// Устанавливаем путь для куки 

	function setCookiePath(string $cookie_name, string $path): bool {

		if (!$this->cookieKeyExist($cookie_name) || empty($path)) {return false;}

		$this->cookies[$cookie_name]['hostDomen'] = $path;

		return true;
	}

	function setCookieTime(string $cookie_name, string $time): bool {

		if (!$this->cookieKeyExist($cookie_name) || empty($time)) {return false;}

		//if (preg_match('+ '.[0-9].'[Years][Days]'., subject))

		$this->cookies[$cookie_name]['cookieTime'] = strtotime($time);

		return true;
	}

	// Очищаем куки, при условии, если они есть

	/*

	function clearCookie(string $cookie_name): bool {
		if (!$this->viewCookie($cookie_name)) {return false;}
		$this->initCookie($cookie_name);
		$this->setCookieTime($cookie_name, '-3700');			
		$this->saveCookie($cookie_name);
		unset($this->cookies[$cookie_name]);
		return true;
	}
	
	*/

	// Достаем данные из уже установленных куки 

	function viewCookie(string $cookie_name=''): ?string {

		if(empty($cookie_name)) { return null; }

		$globals = new GlobalParams();
		$globals->setGlobParam('_COOKIE');

		if($globals->isExist($cookie_name)) {

			return $globals->getGlobParam($cookie_name);
		}

		return null;
	}

	function saveCookie(string $cookie_name): bool {

		return true;
	}
}