<?php 


/**
 *   Класс для работы с куки.
 	Получение, установка, удаление любых куки в разные пути
 */
class CookieJob {
	
	function __construct() { 

	/*
		setcookie(name, value, expire, path, domain, security);

		Parameters: The setcookie() function requires six arguments in general which are:

		Name: It is used to set the name of the cookie.
		Value: It is used to set the value of the cookie.
		Expire: It is used to set the expiry timestamp of the cookie after which the cookie can’t be accessed.
		Path: It is used to specify the path on the server for which the cookie will be available.
		Domain: It is used to specify the domain for which the cookie is available.
		Security: It is used to indicate that the cookie should be sent only if a secure HTTPS connection exists.
	*/

		/*
		$this->cookie = array(
			'empty'	=> array(
				'cvalue' 	=> '',
				'ctime'		=> time()
			) 
		)
		*/
	}

	private $cookie;


	public function setCookies(array $cookies): void {

		if(empty($cookies)) { return; }

		foreach ($cookies as $key => $value) {
			
			$this->cookie[$key]['cvalue'] = $value;
		}
	}

	public function existPrepCookie(string $cookie): bool {

		if(empty($this->cookie)) { return false; }

		return array_key_exists($cookie, $this->cookie);
	}

	// Years, Months, Days, Hours, Minutes, Seconds 

	public function setCookieTime(string $cookieName, string $time, bool $gotopast=false): void {

		if(!$this->existPrepCookie($cookieName)) { return; }	

		$this->cookie[$cookieName]['ctime'] = strtotime($time);

		//if (preg_match('+ '.[0-9].'[Years][Days]'., subject))
	}

	function setPathDomenCookie(string $cookieName, string $path = '/', string $domen = 'localhost'): void {

		if(!$this->existPrepCookie($cookieName)) { return; }	

		$this->cookie[$cookieName]['cpath'] = $path;
		$this->cookie[$cookieName]['cdomen'] = $domen;
	}

	// Debug: func

	function viewCookie(array $cookieName): void{

		$glob = new GlobalParams();
		$glob->setGlobParam('_COOKIE');

		foreach ($cookieName as $key => $value) {
			
			if ($glob->isExist($value)) {

				$cookieValue = $glob->getGlobParam($value);
			} 
		}
	}

	function saveCookie(string $cookieName = ''): void {

		$saveAll = false;

		if(empty($cookieName)) { $saveAll = true; }
		if(empty($this->cookie)) { return; }

		$csaver = function($name, $values) {

			return setcookie($name, $values['cvalue'], $values['ctime'], $values['cpath'], $values['cdomen']);
		}; 

		try {

			$t = true;

			if(!$saveAll) {

				if(!$this->existPrepCookie($cookieName)) { 
					
					throw new RuntimeException('Ошибка куки отсутсвуют!');
				}

				$t = $csaver($cookieName, $this->cookie[$cookieName]);

			} else {

				foreach ($this->cookie as $key => $value) {
					
					if(!$this->existPrepCookie($key)) { continue; }

					$t = $csaver($key, $value);
				}
			}
			
			if (!$t) {
				throw new RuntimeException('Ошибка установки кук!');
			}

		} catch (Exception $e) {

			debugger("Error! " . $e->getMessage(),__METHOD__);
		}
	}

	function clearCookie(string $cookieName): void {

		if(!$this->existPrepCookie($cookieName)) { return; }

		//return ;	

		setcookie($cookie['name'], $cookie['value'], $cookie['time'], $cookie['path'], $cookie['domain']);
	}
}
