<?php 

/*

	Класс для работы с куки 
	cохраниние, поиск, удаление 
*/



final class Cookies{
	
	private $cookies;

	function __construct() {

		$this->cookies = array();
	}

	// Проверяем существует ли параметр в массиве 

	function cookieKeyExist(string $cookie_name=''): bool {

		if (empty($cookie_name)) { 

			throw new Exception('В массиве нету такого параметра!', 1);
		}

		return array_key_exists($cookie_name, $this->cookies) ? true : false;
	}

	// иницилизируем куки

	function initCookie(string $cookie_name): void {

		if ($this->cookieKeyExist($cookie_name)) {return;}

		$this->cookies[$cookie_name] = array(

			'cookieValue'	=> '', // Значение в куках
			'cookiePath' 	=> '', // Путь установки кук
			'cookieDomen'	=> '', // домен на котором устанавливаются куки
			'cookieTime'	=> ''
		);
	}

	// устанавливаем значение куки

	function setCookieValue(string $cookie_name, string $cookie_value): void {

		if (!$this->cookieKeyExist($cookie_name)) { return; }

		// Проверить не пустое ли значение тут???

		$this->cookies[$cookie_name]['cookieValue'] = $cookie_value;
	}

	function setCookieDomen(string $cookie_name, string $cookie_domain): bool {

		if (!$this->cookieKeyExist($cookie_name)) {return false; }

		$this->cookies[$cookie_name]['cookieDomen'] = $cookie_domain;

		return true;
	}


	// Устанавливаем путь для куки 

	function setCookiePath(string $cookie_name, string $path): bool {

		if (!$this->cookieKeyExist($cookie_name) || empty($path)) {return false;}

		$this->cookies[$cookie_name]['cookiePath'] = $path;

		return true;
	}

	function setCookieTime(string $cookie_name, string $time): bool {

		if (!$this->cookieKeyExist($cookie_name) || empty($time)) {return false;}

		//if (preg_match('+ '.[0-9].'[Years][Days]'., subject))

		$this->cookies[$cookie_name]['cookieTime'] = strtotime($time);

		return true;
	}


	// Достаем данные из уже установленных куки 

	function viewCookie(string $cookie_name=''): ?string {

		if (!$this->cookieKeyExist($cookie_name)) { return null; }

		$globals = new GlobalParams();
		$globals->setGlobParam('_COOKIE');

		if($globals->isExist($cookie_name)) {

			return $globals->getGlobParam($cookie_name);
		}

		return null;
	}

	function cleanMapArray(string $cookie_name): void {

		if (!$this->cookieKeyExist($cookie_name)) { return; }

		unset($this->cookies[$cookie_name]);
	}

	function saveCookie(string $cookie_name): bool {

		if (!$this->cookieKeyExist($cookie_name)) { return false; }


		$cookie = $this->cookies[$cookie_name];
		$cookie['name'] = $cookie_name;
		//debugger($cookie);

		$result = setcookie($cookie_name, $cookie['cookieValue'], $cookie['cookieTime'], $cookie['cookiePath'], $cookie['cookieDomen']);

		if(!$result) {
			throw new Exception('Ошибка куки не установленны!', 1);
		}

		return true;
	}
}