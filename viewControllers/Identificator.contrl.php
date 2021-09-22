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

			/*	
	
				'useridMaxSym'		 => 255,
				'useridMinSym'		 =>1,

				'userNameMaxSym'	 => 100,
				'userNameMinSym' 	 => 2,

				'userEmailMaxSym' 	 => 100,
				'userEmailMinSym' 	 => 6,

				'userPasswordMaxSym' => 55,
				'userPasswordMaxSym' => 8,

				'tokensHashMaxSym' 	 => 100,
				'tokensHashMinSym'	 => 8				
	
				'confirmHashMaxSym'  => 100,
				'confirmHashMinSym'  => 8,

				'recoveryHashMaxSym' => 100,
				'recoveryHashMinSym' => 8,

			*/

			'loginEmailMaxSym'=> 55,
			'loginEmailMinSym'=> 7,

			'loginPasswordMax'=> 55,
			'loginPasswordMin'=> 8,

			// Данные из куки

			'tokenMailMaxSym' => 55,
			'tokenMailMinSym' => 7,

			'tokenHashMaxSym' => 500,
			'tokenHashMinSym' => 8,

			// Данные

			'useridMaxSym'	  => 255,
			'useridMinSym'	  => 1,

			'confirmKeyMaxSym'=> 500,
			'confirmKeyMinSym'=> 8,

			'recovTokenMaxSym'=> 500,
			'recovTokenMinSym'=> 8,

			'usernameMaxSym'  => 20,
			'usernameMinSym'  => 2

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

		if(!$redirect || !defined('LOGOUT')) { return true; }

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
			return false;
		}

		$this->glob->setGlobParam('_POST');

		$loginParams = array(

			'loginmail' 	=> false,
			'loginpasswd' 	=> false
		);

		foreach ($loginParams as $key => $value) {
			
			// Фильтруем основные веши!

			// Это условие отрабатывает всегда!! -------
			if(!$this->glob->isExist($key)) { return false; }

			$value = $this->glob->getGlobParam($key);

			if(!$this->isNotEmpty($value)) { 

				Logger::collectAlert('warnings', 'У вас есть пустые поля!');
				return false;  
			}

			//$value = $this->mainSanitizer($value, 'encoding');
			$value = $this->mainSanitizer($value, 'magicquotes');
			$value = $this->mainSanitizer($value, 'fullspecchars');
			$value = $this->mainSanitizer($value, 'string');
			$value = $this->mainSanitizer($value, 'stripped');

			try {
				$value = $this->ejectedWords($value);
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

			if ($this->isMoreThan($value, $max)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей превышенно максимальное кол-во символов!');
				return false;
			}

			if($this->isLessThen($value, $min)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей количество символов меньше разрешенного!');
				return false; 
			}

			$loginParams[$key] = $value;
		}

		// фильтруем по уникальности -----------------

		if(!$this->mainValidator($loginParams['loginmail'], 'email')) {

			Logger::collectAlert('warnings', 'Указан некорректный емайл!');
			return false; 
		}

		$userExist = $this->users->userExist($loginParams['loginmail']);

		if(!$userExist) {

			Logger::collectAlert('warnings', 'Указанный пользователь не найден или удален!');
			return false; 
		}

		$userNotBlocked = $this->auth->userActivated($loginParams['loginmail']);

		if (!$userNotBlocked) {

			Logger::collectAlert('warnings', 'Указанный пользователь отправлен в бан!');
			return false;
		}

		$findUser = $this->auth->findUser($loginParams['loginmail'], $loginParams['loginpasswd']);

		if(empty($findUser) || !array_key_exists('userid', $findUser)) {

			Logger::collectAlert('warnings', 'Неправильные имя или пароль!');
			return false;
		}

		// тут получаем хеш, если нету то генерируется новый 

		$findUser['tokenHash'] = $this->auth->updateUserHash($findUser['userid'], false);

		if(empty($findUser['tokenHash'])) {

			Logger::collectAlert('warnings', 'Ошибка генерации хеш кода!');
			return false;
		}

		$isItSaved = $this->saveAuthAction($findUser['useremail'], $findUser['tokenHash'], false, true);

		if(!$isItSaved) {

			Logger::collectAlert('warnings', 'Ошибка! не могу сохранить данные, возможно у вас отключены куки!');
			return false;
		} 


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
			
			if(!$this->isNotEmpty($value) && !$goPast) { return false; }

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
				
				if($showerr) { Logger::collectAlert('warning', $e->getMessage()); }		
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
			
			if(!$this->glob->isExist($key)) { return $this->setUserProfile(false); }

			$value = $this->glob->getGlobParam($key);

			if(!$this->isNotEmpty($value)) { return $this->setUserProfile(false); }

			//$value = $this->mainSanitizer($value, 'encoding');
			$value = $this->mainSanitizer($value, 'magicquotes');
			$value = $this->mainSanitizer($value, 'fullspecchars');
			$value = $this->mainSanitizer($value, 'string');
			$value = $this->mainSanitizer($value, 'stripped');

			try {
				$value = $this->ejectedWords($value);
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

		if(!$this->mainValidator($authParams['emailhash'], 'email')) { return $this->setUserProfile(false); }

		$userExist = $this->users->userExist($authParams['emailhash']);

		if(!$userExist) { return $this->setUserProfile(false); }

		$userNotBlocked = $this->auth->userActivated($authParams['emailhash']);

		if (!$userNotBlocked) { return $this->setUserProfile(false); }

		$findUser = $this->auth->authUser($authParams['emailhash'], $authParams['tokenhash']);

		if(!$this->isNotEmpty($findUser) || !array_key_exists('userid', $findUser)) { return $this->setUserProfile(false); }

		// Тут обновляем хеш, если закончилось время

		$findUser['tokenhash'] = $this->auth->updateUserHash($findUser['userid'], false);


		if(empty($findUser['tokenhash'])) { return $this->setUserProfile(false); }

		$isItSaved = $this->saveAuthAction($findUser['useremail'], $findUser['tokenhash'], false, true);

		if(!$isItSaved) { return $this->setUserProfile(false); } 

		$this->setUserProfile($findUser);

		return true;
	}


	// Тут нужно установить брать данные из параметров метода, а не с _POST 

	function restoreAction(): ?string {

		if(!defined('RESTOREALLOW') || !RESTOREALLOW ) {

			Logger::collectAlert('warnings', 'Восстановление профиля отключено администратором!');
			return false;
		}

		$this->glob->setGlobParam('_POST');

		$restoreParams = array(

			'restoremail' => false
		);

		foreach ($restoreParams as $key => $value) {
			
			// Фильтруем основные веши!
			// Это условие отрабатывает всегда!! -------
			if(!$this->glob->isExist($key)) { return false; }

			$value = $this->glob->getGlobParam($key);

			if(!$this->isNotEmpty($value)) { 

				Logger::collectAlert('warnings', 'У вас есть пустые поля!');
				return false;  
			}

			//$value = $this->mainSanitizer($value, 'encoding');
			$value = $this->mainSanitizer($value, 'magicquotes');
			$value = $this->mainSanitizer($value, 'fullspecchars');
			$value = $this->mainSanitizer($value, 'string');
			$value = $this->mainSanitizer($value, 'stripped');

			try {
				$value = $this->ejectedWords($value);
			} catch (Exception $e) {
				Logger::collectAlert('warnings', $e->getMessage());
			}

			$max = $this->authParams['loginEmailMaxSym'];
			$min = $this->authParams['loginEmailMinSym'];
		

			if ($this->isMoreThan($value, $max)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей превышенно максимальное кол-во символов!');
				return false;
			}

			if($this->isLessThen($value, $min)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей количество символов меньше разрешенного!');
				return false; 
			} 

			$restoreParams[$key] = $value;
		}

		// фильтруем по уникальности -----------------

		if(!$this->mainValidator($restoreParams['restoremail'], 'email')) {

			Logger::collectAlert('warnings', 'Указан некорректный емайл!');
			return false; 
		}

		$userExist = $this->users->userExist($restoreParams['restoremail']);

		if(!$userExist) {

			Logger::collectAlert('warnings', 'Указанный пользователь не найден или удален!');
			return false; 
		}

		$userNotBlocked = $this->auth->userActivated($restoreParams['restoremail']);

		if (!$userNotBlocked) {

			Logger::collectAlert('warnings', 'Указанный пользователь отправлен в бан!');
			return false;
		}

		$genResult = $this->auth->generateActivations($restoreParams['restoremail']);

		if(!$genResult) {

			Logger::collectAlert('warnings', 'Ошибка, не смог сгенерировать активационный хещ!');
			return false;
		}

		// TODO: Отправка емайла пользователю для восстановления пароля
		// TODO: сделать генерацию ссылок

		$link = HOST.'/verifyrestorerequest/?userid=' . $genResult['id'] . '&confirm=' . $genResult['cofirm'] . '&token=' . $genResult['token'];

		Logger::collectAlert('information', $link);	

		return null;
	}


	function verifyUserActivation(): ?array {

		if(!defined('RESTOREALLOW') || !RESTOREALLOW ) {

			Logger::collectAlert('warnings', 'Восстановление профиля отключено администратором!');
			return null;
		}

		$this->glob->setGlobParam('_GET');

		$restoreParams = array(

			'userid' => false,
			'confirm'=> false,
			'token'	 => false
		);

		foreach ($restoreParams as $key => $value) {
			

			// Фильтруем основные веши!

			// Это условие отрабатывает всегда!! -------
			if(!$this->glob->isExist($key)) { return null; }

			$value = $this->glob->getGlobParam($key);

			if(!$this->isNotEmpty($value)) { 

				Logger::collectAlert('warnings', 'Один из активационных параметров пустой!');
				return null;  
			}

			//$value = $this->mainSanitizer($value, 'encoding');
			$value = $this->mainSanitizer($value, 'magicquotes');
			$value = $this->mainSanitizer($value, 'fullspecchars');
			$value = $this->mainSanitizer($value, 'string');
			$value = $this->mainSanitizer($value, 'stripped');

			try {
				$value = $this->ejectedWords($value);
			} catch (Exception $e) {
				Logger::collectAlert('warnings', $e->getMessage());
			}

			if ($key == 'userid') {
				$max = $this->authParams['useridMaxSym'];
				$min = $this->authParams['useridMinSym'];
			} else if ($key == 'confirm') {
				$max = $this->authParams['confirmKeyMaxSym'];
				$min = $this->authParams['confirmKeyMinSym'];
			} else {
				$max = $this->authParams['recovTokenMaxSym'];
				$min = $this->authParams['recovTokenMinSym'];
			}

			if ($this->isMoreThan($value, $max)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей превышенно максимальное кол-во символов!');
				return null;
			}

			if($this->isLessThen($value, $min)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей количество символов меньше разрешенного!');
				return null;
			}

			$restoreParams[$key] = $value;
		}

		if(!$this->auth->verifyActivations($restoreParams['userid'], $restoreParams['token'], $restoreParams['confirm'])) {
			
			Logger::collectAlert('warnings', 'Ошибка параметров подтверждения пользователя!');
			return null;
		}

		return array(
			'userid' => $restoreParams['userid'],
			'token'	 => $restoreParams['token'],
			'confirm'=> $restoreParams['confirm']
		);
	}

	// verifybyid ключ используется для восстановления ключя, без должен использоваться 


	function updateUserPassword(bool $verifbyid=true, int $userid=0): bool {

		if(!defined('RESTOREALLOW') || !RESTOREALLOW ) {

			Logger::collectAlert('warnings', 'Восстановление профиля отключено администратором!');
			return false;
		}

		// Это условие

		if ($verifbyid) {

			$verified = $this->verifyUserActivation();
			if (!$this->isNotEmpty($verified)) { 

				Logger::collectAlert('warnings', 'Проверка не пройдена!');
				return false;
			}
		}

		$updateParams = array(

			'newpassword1' => false,
			'newpassword2' => false
		);

		$this->glob->setGlobParam('_POST');

		foreach ($updateParams as $key => $value) {
			
			// Фильтруем основные веши!

			// Это условие отрабатывает всегда!! -------
			if(!$this->glob->isExist($key)) { return false; }

			$value = $this->glob->getGlobParam($key);

			if(!$this->isNotEmpty($value)) { 

				Logger::collectAlert('warnings', 'У вас есть пустые поля!');
				return false;  
			}

			//$value = $this->mainSanitizer($value, 'encoding');
			$value = $this->mainSanitizer($value, 'magicquotes');
			$value = $this->mainSanitizer($value, 'fullspecchars');
			$value = $this->mainSanitizer($value, 'string');
			$value = $this->mainSanitizer($value, 'stripped');

			try {
				$value = $this->ejectedWords($value);
			} catch (Exception $e) {
				Logger::collectAlert('warnings', $e->getMessage());
			}

			$max = $this->authParams['loginPasswordMax'];
			$min = $this->authParams['loginPasswordMin'];
			
			if ($this->isMoreThan($value, $max)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей превышенно максимальное кол-во символов!');
				return false;
			}

			if($this->isLessThen($value, $min)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей количество символов меньше разрешенного!');
				return false; 
			}

			$updateParams[$key] = $value;
		}

		if ($updateParams['newpassword1'] !== $updateParams['newpassword2']) {

			Logger::collectAlert('warnings', 'Ошибка! пароли не совпадают.');

			return false;
		}

		$result = $this->users->updateUserPassword($verified['userid'], $updateParams['newpassword1'], true);

		if (!$result) {

			Logger::collectAlert('warnings', 'Ошибка обновления пароля!');
			return false;
		}

		$r = $this->auth->clearActivations($verified['userid']);

		debugger($r);

		Logger::collectAlert('success', 'Пароль обновлен!');

		return true;
	}


	function registrationAction(): ?string {

		if(!defined('REGISTRATIONALLOW') || !REGISTRATIONALLOW) {

			Logger::collectAlert('warnings', 'Регистрация отключено администратором!');
			return null;
		}

		$registrationParams = array(

			'userregemail' 		=> false,
			'userregname'		=> false,
			'userregpassword1'	=> false,
			'userregpassword2'	=> false
		);

		$this->glob->setGlobParam('_POST');

		foreach ($registrationParams as $key => $value) {
			
			// Фильтруем основные веши!

			// Это условие отрабатывает всегда!! -------
			if(!$this->glob->isExist($key)) { return null; }

			$value = $this->glob->getGlobParam($key);

			if(!$this->isNotEmpty($value)) { 

				Logger::collectAlert('warnings', 'У вас есть пустые поля!');
				return null;  
			}

			//$value = $this->mainSanitizer($value, 'encoding');
			$value = $this->mainSanitizer($value, 'magicquotes');
			$value = $this->mainSanitizer($value, 'fullspecchars');
			$value = $this->mainSanitizer($value, 'string');
			$value = $this->mainSanitizer($value, 'stripped');

			try {
				$value = $this->ejectedWords($value);
			} catch (Exception $e) {
				Logger::collectAlert('warnings', $e->getMessage());
			}

			if($key == 'userregemail') {
				$max = $this->authParams['loginEmailMaxSym'];
				$min = $this->authParams['loginEmailMinSym'];
			} else if ($key == 'userregname') {
				$max = $this->authParams['usernameMaxSym'];
				$min = $this->authParams['usernameMinSym'];
			} else {
				$max = $this->authParams['loginPasswordMax'];
				$min = $this->authParams['loginPasswordMin'];
			}

			if ($this->isMoreThan($value, $max)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей превышенно максимальное кол-во символов!');
				return null;
			}

			if($this->isLessThen($value, $min)) {

				Logger::collectAlert('warnings', 'Ошибка! В одном из полей количество символов меньше разрешенного!');
				return null; 
			}

			$registrationParams[$key] = $value;
		}

		if(!$this->mainValidator($registrationParams['userregemail'], 'email')) {

			Logger::collectAlert('warnings', 'Указан некорректный емайл!');
			return null; 
		}


		if ($registrationParams['userregpassword1'] !== $registrationParams['userregpassword2']) {

			Logger::collectAlert('warnings', 'Ошибка! пароли не совпадают.');
			return null;
		}


		$userExist = $this->users->userExist($registrationParams['userregemail']);

		if($userExist) {

			Logger::collectAlert('warnings', 'Такой пользователь уже зарегестрирован!');
			return null; 
		}

		$insert = $this->users->insertNewUser($registrationParams['userregemail'], $registrationParams['userregpassword1'], $registrationParams['userregname']);

		if (!$insert) {

			Logger::collectAlert('warnings', 'Ошибка регистрации пользователя! Не могу добавить пользователя.'); 
			return null;
		}

		$meta = $this->auth->generateActivations($registrationParams['userregemail']);

		if(!$meta) {
			Logger::collectAlert('warnings', 'Ошибка генерации ccылки для активации! Внимание обратитесь к администратору!!!');
			return null;
		}

		// TODO: Отправка емайла пользователю для восстановления пароля
		// TODO: сделать генерацию ссылок

		$link = HOST . '/verifreg/?userid=' . $meta['id'] . '&confirm=' . $meta['cofirm'] . '&token=' . $meta['token'];

		Logger::collectAlert('information', $link);
		return $link;

	}


	function verifyUserRegistration(): bool {

		if(!defined('REGISTRATIONALLOW') || !REGISTRATIONALLOW ) {

			Logger::collectAlert('warnings', 'Регистрация отключено администратором!');
			return false;
		}

		$registrationConfirm = $this->verifyUserActivation();

		if(!$this->isNotEmpty($registrationConfirm)) {
			Logger::collectAlert('warnings', 'Параметры регистрационных данных не правильные!');
			return false;
		}

		$status = $this->auth->activateRegisteredUser($registrationConfirm['userid']);

		if (!$status) {

			Logger::collectAlert('warnings', 'Ошибка активации пользователя!');
			return false;
		}

		$this->auth->clearActivations($registrationConfirm['userid']);

		Logger::collectAlert('success', 'Аккаунт активирован!');
		return true;
	}


	// Функция которая возвращает данные из вне чтобы не дублировать код впихнул все сюда 

	function getInputParams(): ?array {


	}
}






























