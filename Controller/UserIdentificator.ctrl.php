<?php

class UserIdentificator {

	private $cjob;
	private $auth;
	private $glob;
	private $filter;
	private $cookies;
	private $pluginExecutor;

	private $users;
	private $granter; // Получаем привелегии пользователя 

	private $defineUser;

	private $errors;

	function __construct() {

		$this->cjob 	= new CookieJob();
		$this->auth 	= new Auth();
		$this->glob 	= new GlobalParams();
		$this->filter 	= new Filter();
		$this->users 	= new Users();
		$this->granter 	= new PrivelegesController();

		$this->errors 	= 0;

		$this->AuthParams = array(
			'future' 	=> '+2 Hours',
			'past' 		=> '-2 Hours',
			'host' 		=> '/',
			'domain' 	=> 'localhost',
		);

		// Замыкание которое возвращает результат как профиль пользователя

		$this->defineUser = function($profile=''):bool {

			$temp = array(

			    'userid' 		=> 0,
	            'username' 		=> 'Анонимный пользователь!',
	            'priveleges'	=> 'Гость/Guest',
			);

			$auth = false;

			if(!empty($profile)) {

				foreach ($profile as $key => $value) {
				
					$temp[$key] = $value;
				}

				$this
					->granter
					->initUser($profile['userid']);

				$uperms = $this
							->granter
							->getPermsOfUser();

				$temp['priveleges'] = implode(', ', $uperms);
				$auth = true;
			}

			// Определяем профиль в независимости от авторизации

			if(!defined('PROFILE')) {

				define('PROFILE', $temp);
			}

			return $auth;
		};
	}

	// ----------------------------------------------

	private function filtration(string $input, array $options): string{

		$this->filter->setVariables(
				array(
					'key' => array(
						'value' => $input,
						'maximum' => $options['maxSym'],
						'minimum' => $options['minSym'],
					),
				)
			);

		// TODO: filtration: eazy, medium, hard
		$this
			->filter
			->cleanAttack('key', array(''));

		if ($options['checkMail'] && !$this->filter->validator('key', 'email')) {

			$this->errors++;
			Logger::collectAlert('warnings', 'Ошибка! Укажите правильный емайл');
		}

		// конвертирует в целое число 
		if(isset($options['getNumber']) && $options['getNumber']){

			$this
				->filter
				->convertToNumber('key');
		}

		if (!$this->filter->isNotEmpty('key')) {

			$this->errors++;
			Logger::collectAlert('warnings', 'Недопустима пустая строка!');

		} else if (!$this->filter->isNotMore('key')) {

			$this->errors++;
			Logger::collectAlert('warnings', 'Недопустимое кол-во символов! Большое значение.');

		} else if (!$this->filter->isNotLess('key')) {

			$this->errors++;
			Logger::collectAlert('warnings', 'Недопустимое кол-во символов! Маленькое значение.');
		}

		return $this
					->filter
					->getKey('key');
	}

	// ----------------------------------------------

	function logout(bool $redirect = false, bool $clean = false): bool{

		$this
			->glob
			->setGlobParam('_GET');

		if ($this->glob->isExist('logout') || $clean) {

			// пустые емай и хеш и стираем данные
			$this->saveAuthAction('', '', true); 

			if ($redirect || LOGOUT['redirectuser']) {

				header('refresh:'.LOGOUT['timeout'].'; url=' . LOGOUT['redirectpath']); }

			$this->errors++;
			Logger::collectAlert('information', 'Вы вышли из своего аккаунта!');

			return true;
		}
		// Установить сессию

		return false;
	}

	// ----------------------------------------------

	function loginAction(): bool{

		$param = array(

			'loginmail' => array(
					'value'		=> null, 
					'maximum' 	=> 40,
					'minimum' 	=> 4,
					'checkMail' => true,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html')
					//'cutIt'		=> true;
			),
			'loginpasswd' => array(
					'value'		=> null, 
					'maximum' 	=> 40,
					'minimum' 	=> 4,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html')
			)
		);

		$p = array(); 				// Тут будет результат
		$prof = $this->defineUser; 	// Устанавливаем сразу анонимного пользователя 

		$this
			->glob
			->setGlobParam('_POST');

		foreach ($param as $key => $value) {
			
			// если нету параметра устанавливаем анонимного пользователя
			if(!$this->glob->isExist($key)) { return $prof(); }

			$param[$key]['value'] = $this->glob->getGlobParam($key);

			$this->filter->setVariables($param);

			$this->filter->letsFilterIt($key);

			$errors = $this->filter->getFilterErrors();

			if (!empty($errors) && count($errors) > 0) {

				foreach ($errors as $errKey => $errValue) {
					
					Logger::collectAlert('warnings', $errValue);
				}

				return $prof();
			} 

			$p[$key] = $this->filter->getKey($key);
		}

		$ue = $this
				->users
				->userExist($p['loginmail']);
		$ub = $this
				->auth
				->userActivated($p['loginmail']);

		if (!$ue) {

			Logger::collectAlert('warnings', 'Неправильные имя или пароль!');
			return $prof();
		} 

		if (!$ub) {

			Logger::collectAlert('warnings', 'Пользователь заблокирован или не активирован.');
			return $prof();
		}

		$profile = $this
						->auth
						->findUser($p['loginmail'], $p['loginpasswd']);

		if(empty($profile)) {

			Logger::collectAlert('warnings', 'Неправильные имя или пароль!');
			return $prof();
		}

		$redirection = array();

		if (!defined('REDIRECTLOGIN')) {

			$redirection = array(
							'redirectuser' 	=> false,
			);
		} else {
			$redirection = REDIRECTLOGIN;

			// если по умолчанию перенаправляет на профиль пользователя

			$redirection['redirectpath'] = str_replace('%userid%', $profile['userid'], $redirection['redirectpath']);
		}

		$profile['tokenHash'] = $this
									->auth
									->updateUserHash($profile['userid'], false);

		if(empty($profile['tokenHash'])) {

			Logger::collectAlert('warnings', 'Ошибка генерации хеша!');
			return $prof(); 
		}

		$this->saveAuthAction($p['loginmail'], $profile['tokenHash'], false, true);

		if ($redirection['redirectuser']) { 

			header('refresh: '.$redirection['timeout'].'; url='.$redirection['redirectpath']);
		}

		Logger::collectAlert('success', 'Вы вошли в свой аккаунт!');
	
		return $prof($profile);
	}

	// ----------------------------------------------

	function catchAndRedirectBackUser() {

		// Отловить страницу с которой пользователь пришел и отправить его туда же.

	}

	function authAction(): bool {

		$param = array(

			'mailhash' => array(

					'value'		=> null, 
					'maximum' 	=> 500,
					'minimum' 	=> 4,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html')
			),
			'tokenhash' => array(

					'value'		=> null, 
					'maximum' 	=> 500,
					'minimum' 	=> 4,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html')
			)
		);

		$p = array(); 				// Тут будет результат
		$prof = $this->defineUser; 	// Устанавливаем сразу анонимного пользователя 

		$this
			->glob
			->setGlobParam('_COOKIE');

		foreach ($param as $key => $value) {
			
			// если нету параметра устанавливаем анонимного пользователя
			if(!$this->glob->isExist($key)) { return $prof(); }

			$param[$key]['value'] = $this->glob->getGlobParam($key);

			$this->filter->setVariables($param);

			$this->filter->letsFilterIt($key);

			$errors = $this->filter->getFilterErrors();

			if (!empty($errors) && count($errors) > 0) {

				foreach ($errors as $errKey => $errValue) {
					
					Logger::collectAlert('warnings', $errValue);
				}

				return $prof();
			} 

			$p[$key] = $this->filter->getKey($key);
		}

		$ue = $this
					->users
					->userExist($p['mailhash']);
		$ub = $this
					->auth
					->userActivated($p['mailhash']);

		if (!$ue || !$ub) { return $prof(); }

		$profile = $this
						->auth
						->authUser($p['mailhash'], $p['tokenhash']);

		if(!empty($profile)) {

			$profile['tokenHash'] = $this
										->auth
										->updateUserHash($profile['userid'], false);

			$this->saveAuthAction($profile['useremail'], $profile['tokenHash']);

			return $prof($profile);
		} else {
			// Стираем данные кук
			$this->saveAuthAction(false, false, true);
		}

		return $prof();
	}

	// Устанавливаем куки и сессию или удаляем их в зависимости от переменной $gopast

	// TODO: => перенести в COOKIEJOB

	private function saveAuthAction(string $email = '', string $hash = '', bool $gopast = false, bool $showerr=false): void{

		$time = !$gopast ? $this->AuthParams['future'] : $this->AuthParams['past'];

		$authParams = array(
			'mailhash' => $email,
			'tokenhash' => $hash,
		);
		$this
			->cjob
			->setCookies($authParams);

		$errors = array();

		foreach ($authParams as $key => $value) {

			$this
				->cjob
				->setCookieTime($key, $time);
			$this
				->cjob
				->setPathDomenCookie($key, $this->AuthParams['host'], $this->AuthParams['domain']);
			$r = $this
					->cjob
					->saveCookie($key);

			if (!$r && $showerr) {

				Logger::collectAlert('warnings', 'Не могу установить/стереть куки!');
			}		
		}
	}
	
	// ----------------------------------------------

	function resAction(): ?string {

		$param = array(

			'restoremail' => array(
					'value'		=> null, 
					'maximum' 	=> 40,
					'minimum' 	=> 4,
					'checkMail' => true,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html')
					//'cutIt'		=> true;
			),
		);

		$p = array(); 				// Тут будет результат
		$prof = $this->defineUser; 	// Устанавливаем сразу анонимного пользователя 

		$this
			->glob
			->setGlobParam('_POST');

		foreach ($param as $key => $value) {
			
			// если нету параметра устанавливаем анонимного пользователя
			if(!$this->glob->isExist($key)) { return $prof(); }

			$param[$key]['value'] = $this->glob->getGlobParam($key);

			$this->filter->setVariables($param);

			$this->filter->letsFilterIt($key);

			$errors = $this->filter->getFilterErrors();

			if (!empty($errors) && count($errors) > 0) {

				foreach ($errors as $errKey => $errValue) {
					
					Logger::collectAlert('warnings', $errValue);
				}

				return $prof();
			} 

			$p[$key] = $this->filter->getKey($key);
		}

		$mr = $this
				->users
				->userExist($p['restoremail']); 
		$ms = $this
				->auth
				->userActivated($p['restoremail']);

		if (!$mr || !$ms) {

			Logger::collectAlert('warnings', 'Ошибка! Возможно пользователь: заблокирован, удален или не существует!');

			return null;
		}

		$meta = $this
			->auth
			->generateActivations($p['restoremail']);

		if (empty($meta)) {return null;}

		// TODO: Отправка емайла пользователю для восстановления пароля
		// TODO: сделать генерацию ссылок

		$link = '/verifres/?userid=' . $meta['id'] . '&confirm=' . $meta['cofirm'] . '&token=' . $meta['token'];

		Logger::collectAlert('information', $link);		

		return true;
	}



	// ----------------------------------------------

	function verifyUserModifications(): ?array{


		$param = array(

			'userid' => array(
					'value'		=> null, 
					'maximum' 	=> 5,
					'minimum' 	=> 4,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html'),
					'getNumber'	=> true
			),
			'confirm' => array(
					'value'		=> null, 
					'maximum' 	=> 500,
					'minimum' 	=> 5,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html'),
					'getNumber'	=> true
			),
			'token' => array(
					'value'		=> null, 
					'maximum' 	=> 500,
					'minimum' 	=> 5,
					'checkMail' => true,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html'),
					'getNumber'	=> true
			),
		);

		$p = array(); 				// Тут будет результат
		$prof = $this->defineUser; 	// Устанавливаем сразу анонимного пользователя 

		$this
			->glob
			->setGlobParam('_GET');

		foreach ($param as $key => $value) {
			
			// если нету параметра устанавливаем анонимного пользователя
			if(!$this->glob->isExist($key)) { return $prof(); }

			$param[$key]['value'] = $this->glob->getGlobParam($key);

			$this->filter->setVariables($param);

			$this->filter->letsFilterIt($key);

			$errors = $this->filter->getFilterErrors();

			if (!empty($errors) && count($errors) > 0) {

				foreach ($errors as $errKey => $errValue) {
					
					Logger::collectAlert('warnings', $errValue);
				}

				return $prof();
			} 

			$p[$key] = $this->filter->getKey($key);
		}

		$sp = $this
				->auth
				->verifyActivations($p['userid'], $p['token'], $p['confirm']);

		if (!$sp) {

			Logger::collectAlert('warnings', 'Ошибка параметров подтверждения пользователя!');
			return null;
		}

		return $p; // Возвращает id, confirm и token для дальнейшего использования 
	}

	/**
	*   если $verifbyid установлен в false  то нужно указать по $id 
	*/

	function updatePassword(bool $verifbyid=true, int $userid=0): bool {

		if ($verifbyid) {

			$v = $this->verifyUserModifications();

			if (!$v || empty($v)) { return false;}
		}

		$param = array(

			'newpassword1' => array(
					'value'		=> null, 
					'maximum' 	=> 100,
					'minimum' 	=> 6,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html')
			),
			'newpassword2' => array(
					'value'		=> null, 
					'maximum' 	=> 100,
					'minimum' 	=> 6,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html')
			),
		);

		$this
			->glob
			->setGlobParam('_POST');

		foreach ($param as $key => $value) {
			
			// если нету параметра устанавливаем анонимного пользователя
			if(!$this->glob->isExist($key)) { return false; }

			$param[$key]['value'] = $this->glob->getGlobParam($key);

			$this->filter->setVariables($param);

			$this->filter->letsFilterIt($key);

			$errors = $this->filter->getFilterErrors();

			if (!empty($errors) && count($errors) > 0) {

				foreach ($errors as $errKey => $errValue) {
					
					Logger::collectAlert('warnings', $errValue);
				}

				return false;
			} 

			$p[$key] = $this->filter->getKey($key);
		}

		//if ($p[0] !== $p[1]) {
		if ($p['newpassword1'] !== $p['newpassword2']) {

			Logger::collectAlert('warnings', 'Ошибка! пароли не совпадают.');

			return false;
		}

		$r = $this
				->users
				->updateUserPassword($v['userid'], $p[0], true);

		if (!$r) {

			Logger::collectAlert('warnings', 'Ошибка обновления пароля!');
			return false;
		}

		$this->auth->clearActivations($v['userid']);

		Logger::collectAlert('success', 'Пароль обновлен!');

		return true;
	}



	// добавляет нового пользователя в базу данных
	// ----------------------------------------------

	function regAction():  ?string{

		$param = array(

			'userregemail' => array(
					'value'		=> null, 
					'maximum' 	=> 40,
					'minimum' 	=> 4,
					'checkMail' => true,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html')
					//'cutIt'		=> true;
			),
			'userregname' => array(
					'value'		=> null, 
					'maximum' 	=> 40,
					'minimum' 	=> 4,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html')
			),
			'userregpassword1' => array(
					'value'		=> null, 
					'maximum' 	=> 40,
					'minimum' 	=> 4,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html')
			),
			'userregpassword2' => array(
					'value'		=> null, 
					'maximum' 	=> 40,
					'minimum' 	=> 4,
					'cleanHack'	=> true,
					'itsEmpty' 	=> true,
					'itsMore'	=> true,
					'itsLess'	=> true,
					'sanitazer'	=> array('specchars','html')
			)
		);

		$this
			->glob
			->setGlobParam('_POST');

		foreach ($param as $key => $value) {
			
			// если нету параметра устанавливаем анонимного пользователя
			if(!$this->glob->isExist($key)) { return false; }

			$param[$key]['value'] = $this->glob->getGlobParam($key);

			$this->filter->setVariables($param);

			$this->filter->letsFilterIt($key);

			$errors = $this->filter->getFilterErrors();

			if (!empty($errors) && count($errors) > 0) {

				foreach ($errors as $errKey => $errValue) {
					
					Logger::collectAlert('warnings', $errValue);
				}

				return false;
			} 

			$p[$key] = $this->filter->getKey($key);
		}

		if ($p['userregpassword1'] !== $p['userregpassword2']) {

			Logger::collectAlert('warnings', 'Ошибка! пароли не совпадают.');
			return false;
		}

		$e = $this
				->users
				->userExist($p['userregemail']);

		if ($e) {

			Logger::collectAlert('warnings', 'Возможно такой пользователь уже зарегестрирован!');
			return null;
		}

		$insert = $this
					->users
					->insertNewUser($p['userregemail'], $p['userregpassword1'], $p['userregname']);

		if (!$insert) {

			Logger::collectAlert('warnings', 'Не получилось зарегестрироваться!'); 
			Logger::collectAlert('warnings', 'Проверьте еще раз ваши данные.При повторной ошибке обратитесь к администратору!');
			return null;
		}

		$meta = $this
					->auth
					->generateActivations($p['userregemail']);

		if (empty($meta)) {return false;}

		// TODO: Отправка емайла пользователю для восстановления пароля
		// TODO: сделать генерацию ссылок

		$link = HOST . '/verifreg/?userid=' . $meta['id'] . '&confirm=' . $meta['cofirm'] . '&token=' . $meta['token'];

		Logger::collectAlert('information', $link);
		return true;
	}

	function verifyRegistration(): bool{

		// Добавляем последний визит, привелегии пользователю
		// -------

		$p = $this->verifyUserModifications();

		if (!$p || empty($p)) {return false;}

		$astatus = $this
						->auth
						->activateRegisteredUser($p['userid']);

		if (!$astatus) {

			Logger::collectAlert('warnings', 'Ошибка активации пользователя!');
			return false;
		}

		$this
			->auth
			->clearActivations($p['userid']);

		Logger::collectAlert('success', 'Аккаунт активирован!');
		return true;
	}
}