<?php 

// Класс идентификации пользователя

class Identificator extends Filter {
	
	function __construct() {
		
		$this->authParams = array(

			// Для куки параметры по умолчанию

			'future' 	=> '+2 Hours',
			'past' 		=> '-2 Hours',
			'host' 		=> '/',
			'domain' 	=> 'localhost',//HOST,	

			// Параметры пользователя по умолчанию

			'userid'	=> 0,
			'username'	=> 'Анонимный пользователь',
			'priveleges'=> false,


			// Использовать из базы сохраненные параметры !!! 

			// Данные при входе, так же для регистрации, и восстановления пароля

			'loginEmailMaxSym'=> 55,
			'loginEmailMinSym'=> 7,

			'loginPasswordMax'=> 55,
			'loginPasswordMin'=> 6,

			// Данные из куки

			'tokenMailMaxSym' => 500,
			'tokenMailMinSym' => 7,

			'tokenHashMaxSym' => 500,
			'tokenHashMinSym' => 7

			// Данные с проверки подтверждения авторизации
		);

		$this->cjob 	= new Cookies();
		$this->auth 	= new Auth();
		$this->glob 	= new GlobalParams();
		$this->users 	= new Users();
		$this->granter 	= new PrivelegesController();
	}

	private $cjob;
	private $auth;
	private $glob;
	private $users;
	private $granter;
	private $authParams;


	function setUserProfile($profile=''): bool {

		$profileStatusInit = false;

		// Достаем дефольтные данные (статичные)
		$userProfile = array(

			'userid' 	=> $this->authParams['userid'],
			'username' 	=> $this->authParams['username'],
			'priveleges'=> $this->authParams['priveleges']
		);

		if($this->isNotEmpty($profile) && array_key_exists('userid', $profile)) { 

			foreach ($profile as $key => $value) {
				
				$userProfile[$key] = $value;
			}

			$this->granter->initUser($profile['userid']);
			$perms = $this->granter->getPermsOfUser();

			if ($this->isNotEmpty($perms)) {

				$userProfile['priveleges'] = implode(', ', $perms);
			}

			$profileStatusInit = true;
		}

		if(!defined('PROFILE')) {
			define('PROFILE',$userProfile);
		}

		return $profileStatusInit;
	}

	// переменная это принудительный выход из системы, если даже нету _GET => logout параметра!

	function logout(bool $redirect=false, bool $permQuit=false): bool {

		$this->glob->setGlobParam('_GET');

		if(!$this->glob->isExist('logout') && !$permQuit) { return false; }

		$this->saveAuthAction('','',true,true);

		if(!$redirect && !defined('LOGOUT')) { return true; }

		if(LOGOUT['redirectuser']) {

			if (LOGOUT['timeout'] > 0) {
				header('refresh:'.LOGOUT['timeout'].'; url=' . LOGOUT['redirectpath']); 
			} else {
				header('Location: '. LOGOUT['redirectpath']);
			}
		}
		return true;
	}



	// МЕТОД ДЛЯ ВХОДА В СИСТЕМУ ------------------------

	function loginAction(): bool {

		if(!defined('LOGINALLOW') || !LOGINALLOW) {

			Logger::collectAlert('warnings', 'Вход в систему отключен администратором!');
			return $this->setUserProfile(false);
		}

		$this->glob->setGlobParam('_POST');

		$loginParams = array(

			'loginmail' 	=> false,
			'loginpasswd' 	=> false
		);

		foreach ($loginParams as $key => $value) {
			
			// Фильтруем основные веши!


			// Это условие отрабатывает всегда!! -------
			if(!$this->glob->isExist($key)) { 

				//Logger::collectAlert('warnings', 'Параметр: '.$key.' отсутсвует!');
				return $this->setUserProfile(false); 
			}

			$value = $this->glob->getGlobParam($key);

			if(!$this->isNotEmpty($value)) { 

				Logger::collectAlert('warnings', 'есть пустые поля!');
				return $this->setUserProfile(false);  
			}

			//$value = $this->mainSanitizer($value, 'encoding');
			$value = $this->mainSanitizer($value, 'magicquotes');
			$value = $this->mainSanitizer($value, 'fullspecchars');
			$value = $this->mainSanitizer($value, 'string');
			$value = $this->mainSanitizer($value, 'stripped');

			try {
				$this->ejectedWords($value);
			} catch (Exception $e) {
				Logger::collectAlert('warnings', $e->getMessage());
			}

			if ($key == 'loginmail') {
				$max = $this->authParams['loginEmailMaxSym'];
				$min = $this->authParams['loginEmailMinSym'];
			} else {
				$max = $this->authParams['loginPasswordMax'];
				$min = $this->authParams['loginPasswordMin'];
			}

			if ($this->isMoreThan($key, $max)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей превышенно максимальное кол-во символов! :'.$key.' '.$value.' '.$max);
				return $this->setUserProfile(false);
			}

			if($this->isLessThen($key, $min)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей количество символов меньше разрешенного!');
				return $this->setUserProfile(false); 
			}

			$loginParams[$key] = $value;
		}

		// фильтруем по уникальности -----------------

		if(!$this->mainValidator($loginParams['loginmail'], 'email')) {

			Logger::collectAlert('warnings', 'Указан некорректный емайл!');
			return $this->setUserProfile(false); 
		}

		$userExist = $this->users->userExist($loginParams['loginmail']);

		if(!$userExist) {

			Logger::collectAlert('warnings', 'Указанный пользователь не найден или удален!');
			return $this->setUserProfile(false); 
		}

		$userNotBlocked = $this->auth->userActivated($loginParams['loginmail']);

		if (!$userNotBlocked) {

			Logger::collectAlert('warnings', 'Указанный пользователь отправлен в бан!');
			return $this->setUserProfile(false);
		}

		$findUser = $this->auth->findUser($loginParams['loginmail'], $loginParams['loginpasswd']);

		if(empty($findUser) || !array_key_exists('userid', $findUser)) {

			Logger::collectAlert('warnings', 'Неправильные имя или пароль!');
			return $this->setUserProfile(false);
		}

		// тут получаем хеш, если нету то генерируется новый 

		$findUser['tokenHash'] = $this->auth->updateUserHash($findUser['userid'], false);

		if(empty($findUser['tokenHash'])) {

			Logger::collectAlert('warnings', 'Ошибка генерации хеш кода!');
			return $this->setUserProfile(false);
		}

		$isItSaved = $this->saveAuthAction($findUser['useremail'], $findUser['tokenHash'], false, true);

		if(!$isItSaved) {

			Logger::collectAlert('warnings', 'Ошибка! немогу сохранить данные, возможно у вас отключены куки!');
			return $this->setUserProfile(false);
		} 

		$this->setUserProfile($findUser);

		Logger::collectAlert('success', 'Вы вошли в свой аккаунт!');

		if(defined('REDIRECTLOGIN') && REDIRECTLOGIN['redirectuser']) {

			$redirect = REDIRECTLOGIN;

			$redirect['redirectpath'] = str_replace('%userid%', $findUser['userid'], $redirect['redirectpath']);

			if($redirect['timeout'] > 0) {
				header('refresh: '.$redirect['timeout'].'; url='.$redirect['redirectpath']);
			} else {
				header('Location: '.$redirect['redirectpath']);
			}
		}

		return true;
	}

	private function saveAuthAction(string $email, string $hash, bool $goPast=false, bool $showerr=DEBUG): bool {

		$authParams = array(

			'emailhash' => $email,
			'tokenhash'	=> $hash
		);

		foreach ($authParams as $key => $value) {

			// Выходим при условии, что если мы пытаемся сохраниться в будущее, а не выйти 
			// переменная указывает на удаление или сохранение
			
			if(!$this->isNotEmpty($value) && !$goPast) { 

				return false; 
			}

			$time = !$goPast ? $this->authParams['future'] : $this->authParams['past'];

			try {
				$this->cjob->initCookie($key);
				$this->cjob->setCookieValue($key, $value);
				$this->cjob->setCookieTime($key, $time);
				$this->cjob->setCookiePath($key, $this->authParams['host']);
				$this->cjob->setCookieDomen($key, $this->authParams['domain']);
				$this->cjob->saveCookie($key);
				$this->cjob->cleanMapArray($key);
			} catch (Exception $e) {
				
				if($showerr) {
					Logger::collectAlert('warning', $e->getMessage());
				}		
			}
		}

		return true;
	}

	function AuthAction(): bool {

		if(!defined('AUTHENTIFCATIONALLOW') || !AUTHENTIFCATIONALLOW) {

			Logger::collectAlert('warnings', 'Авторизация в системе отключена администратором!');
			return $this->setUserProfile(false);
		}

		$authParams = array(

			'emailhash' => false,
			'tokenhash' => false
		);

		$this->glob->setGlobParam('_COOKIE');

		foreach ($authParams as $key => $value) {
			
			if(!$this->glob->isExist($key)) { 

				return $this->setUserProfile(false); 
			}

			$value = $this->glob->getGlobParam($key);

			if(!$this->isNotEmpty($value)) { 

				return $this->setUserProfile(false);  
			}

			//$value = $this->mainSanitizer($value, 'encoding');
			$value = $this->mainSanitizer($value, 'magicquotes');
			$value = $this->mainSanitizer($value, 'fullspecchars');
			$value = $this->mainSanitizer($value, 'string');
			$value = $this->mainSanitizer($value, 'stripped');

			try {
				$this->ejectedWords($value);
			} catch (Exception $e) {
				Logger::collectAlert('warnings', $e->getMessage());
			}
				
			if ($key == 'mailHash') {
				$max = $this->authParams['tokenMailMaxSym'];
				$min = $this->authParams['tokenMailMinSym'];
			} else {
				$max = $this->authParams['tokenHashMaxSym'];
				$min = $this->authParams['tokenHashMinSym'];
			}

			if ($this->isMoreThan($value, $max) || $this->isLessThen($value, $min)) {

				return $this->setUserProfile(false);
			}

			$authParams[$key] = $value;
		}

		// Нужно проверить зашифрован емайл или нет, если да то расшифровываем

		if(!$this->mainValidator($authParams['emailhash'], 'email')) {

			return $this->setUserProfile(false); 
		}

		$userExist = $this->users->userExist($authParams['emailhash']);

		if(!$userExist) {

			return $this->setUserProfile(false); 
		}

		$userNotBlocked = $this->auth->userActivated($authParams['emailhash']);

		if (!$userNotBlocked) {

			return $this->setUserProfile(false);
		}

		$findUser = $this->auth->authUser($authParams['emailhash'], $authParams['tokenhash']);

		//debug($authParams);

		if(empty($findUser) || !array_key_exists('userid', $findUser)) {

			return $this->setUserProfile(false);
		}

		$findUser['tokenhash'] = $this->auth->updateUserHash($findUser['userid'], false);


		if(empty($findUser['tokenhash'])) {

			return $this->setUserProfile(false);
		}

		$isItSaved = $this->saveAuthAction($findUser['useremail'], $findUser['tokenhash'], false, true);

		if(!$isItSaved) {

			return $this->setUserProfile(false);
		} 

		$this->setUserProfile($findUser);

		return true;
	}

}






























