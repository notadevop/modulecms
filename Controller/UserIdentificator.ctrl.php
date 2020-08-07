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

		// Временно, удалить поже 

		$this->plugExec = function (string $category): bool {

			// TODO: сделать исполнения части кода из дополнительных частей
			// Например, если использовать Action Каптчи 

			return true;
		};

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

			if ($redirect) { header("refresh:5; url=" . HOST); }

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


		/*
		$this
			->glob
			->setGlobParam('_POST');

		$params = array('loginmail', 'loginpasswd');
		$p = array();
		$prof = $this->defineUser;

		foreach ($params as $key => $value) {
			
			if(!$this
					->glob
					->isExist($value)) { return $prof(); }

			$Opt = array(
					'maxSym' => 30,
					'minSym' => 4,
					'checkMail' => $value == $params[0] ? true : false,
			);

			$p[$value] = $this
							->glob
							->getGlobParam($value);

			$p[$value] = $this->filtration($p[$value], $Opt);
		}
		*/

		//if ($this->errors > 0) { return $prof();}

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

			Logger::collectAlert('warnings', 'Ошибка! Пользователь заблокирован или не активирован.');
			return $prof();
		}

		$profile = $this
						->auth
						->findUser($p['loginmail'], $p['loginpasswd']);

		if(empty($profile)) {

			Logger::collectAlert('warnings', 'Неправильные имя или пароль!');
			return $prof();
		}

		$profile['tokenHash'] = $this
									->auth
									->updateUserHash($profile['userid'], false);

		if(empty($profile['tokenHash'])) {

			Logger::collectAlert('warnings', 'Ошибка генерации хеша!');
			return $prof(); 
		}

		$this->saveAuthAction($p['loginmail'], $profile['tokenHash']);

		if (REDIRECTLOGIN) { 
			header( "refresh: 5; url=/" );
			//header('Location: /'); 
		}

		Logger::collectAlert('success', 'Вы вошли в свой аккаунт!');
	
		return $prof($profile);
	}

	// ----------------------------------------------

	function catchAndRedirectBackUser() {

		// Отловить страницу с которой пользователь пришел и отправить его туда же.

	}

	function authAction(): bool {

		$this
			->glob
			->setGlobParam('_COOKIE');

		$params = array('mailhash', 'tokenhash');

		$p = array();

		$Opt = array(
				'maxSym' 	=> 500,
				'minSym' 	=> 4,
				'checkMail' => false
		);

		$prof = $this->defineUser; // Идет как замыкание

		foreach ($params as $value) {
			
			// Возвращает false обычно, заканчивая аутентификацию, так, как нету нужных параметров
			if(!$this
					->glob
					->isExist($value)) return $prof(); 

			$p[$value] = $this
							->glob
							->getGlobParam($value);
			$p[$value] = $this->filtration($p[$value], $Opt);
		}

		if ($this->errors > 0) { return $prof(); }

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

	private function saveAuthAction(string $email = '', string $hash = '', bool $gopast = false): void{

		$time = !$gopast ? $this->AuthParams['future'] : $this->AuthParams['past'];

		$authParams = array(
			'mailhash' => $email,
			'tokenhash' => $hash,
		);
		$this
			->cjob
			->setCookies($authParams);

		foreach ($authParams as $key => $value) {

			$this
				->cjob
				->setCookieTime($key, $time);
			$this
				->cjob
				->setPathDomenCookie($key, $this->AuthParams['host'], $this->AuthParams['domain']);
			$this
				->cjob
				->saveCookie($key);
		}
	}
	
	// ----------------------------------------------

	function resAction(): ?string{

		$this
			->glob
			->setGlobParam('_POST');

		$email = $this
					->glob
					->isExist('restoremail');

		if (!$email) {return null;}

		$emailOpt = array(
			'maxSym' => 100,
			'minSym' => 4,
			'checkMail' => true,
		);

		$email = $this
			->glob
			->getGlobParam('restoremail');

		$email = $this->filtration($email, $emailOpt);

		if ($this->errors > 0) { return null;}

		$mr = $this
				->users
				->userExist($email);
		$ms = $this
				->auth
				->userActivated($email);

		if (!$mr || !$ms) {

			Logger::collectAlert('warnings', 'Ошибка! Возможно пользователь: заблокирован, удален или не существует!');

			return null;
		}

		$meta = $this
			->auth
			->generateActivations($email);

		if (empty($meta)) {return null;}

		// TODO: Отправка емайла пользователю для восстановления пароля
		// TODO: сделать генерацию ссылок

		$link = '/verifres/?userid=' . $meta['id'] . '&confirm=' . $meta['cofirm'] . '&token=' . $meta['token'];

		Logger::collectAlert('information', $link);		

		return true;
	}

	/**
	*   если $verifbyid установлен в false  то нужно указать по $id 
	*/

	function updatePassword(bool $verifbyid=true, int $userid=0): bool {

		if ($verifbyid) {

			$p = $this->verifyUserModifications();

			if (!$p || empty($p)) {return false;}
		}

		$this
			->glob
			->setGlobParam('_POST');

		$pass = array('newpassword1', 'newpassword2');

		$Opt = array(
				'maxSym' 	=> 100,
				'minSym' 	=> 6,
				'checkMail' => false,
			);

		foreach ($pass as $key => $value) {

			if (!$this->glob->isExist($value)) {return false;}

			$value = $this
						->glob
						->getGlobParam($value);

			$pass[$key] = $this->filtration($value, $Opt);
		}

		if ($this->errors > 0) { return false;}

		if ($pass[0] !== $pass[1]) {

			Logger::collectAlert('warnings', 'Ошибка! пароли не совпадают.');

			return false;
		}

		$r = $this
				->users
				->updateUserPassword($p['userid'], $pass[0], true);

		if (!$r) {

			Logger::collectAlert('warnings', 'Ошибка обновления пароля!');
			return false;
		}

		$this
			->auth
			->clearActivations($p['userid']);

		Logger::collectAlert('success', 'Пароль обновлен!');

		return true;
	}


	// ----------------------------------------------

	function verifyUserModifications(): ?array{

		$this
			->glob
			->setGlobParam('_GET');

		$params = array('userid', 'confirm', 'token');

		$p = array();

		foreach ($params as $key => $value) {

			$Opt = array(
				'maxSym' 	=> 50,
				'minSym' 	=> ($value == 'userid' ? 1 : 30),
				'checkMail' => false,
				'getNumber'	=> ($value == 'userid' ? true : false),
			);

			if(!$this->glob->isExist($value)) { 

				Logger::collectAlert('warnings', 'Ошибка! параметров подтверждения');
				return null; }

			$p[$value] = $this
							->glob
							->getGlobParam($value);

			$p[$value] = $this->filtration($p[$value], $Opt);
		}

		if ($this->errors > 0) {

			Logger::collectAlert('warnings', 'Ошибка параметров подтверждения пользователя!');

			return null;
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

	// добавляет нового пользователя в базу данных
	// ----------------------------------------------

	function regAction():  ?string{

		$this
			->glob
			->setGlobParam('_POST');

		$params = array(

			'userregemail',
			'userregname',
			'userregpassword1',
			'userregpassword2'
		);

		$p = array();

		foreach ($params as $value) {
			
			$paramOpt = array(

				'maxSym' 	=> 30,
				'minSym' 	=> ('userregname' == $value ? 4 : 6),
				'checkMail' => ('userregemail' == $value ? true : false)
			);

			if(!$this->glob->isExist($value)) { return null; }

			$p[$value] = $this
							->glob
							->getGlobParam($value);

			$p[$value] = $this->filtration($p[$value], $paramOpt);
		}

		if ($this->errors > 0) { return null; }

		if ($p['userregpassword1'] !== $p['userregpassword2']) {

			Logger::collectAlert('warnings', 'Ошибка! пароли не совпадают.');
			return false;
		}

		$e = $this
				->users
				->userExist($p['userregemail']);

		if ($e) {

			Logger::collectAlert('warnings', 'Ошибка! Возможно такой пользователь уже зарегестрирован!');

			return null;
		}

		$insert = $this
					->users
					->insertNewUser($p['userregemail'], $p['userregpassword1'], $p['userregname']);

		if (!$insert) {

			Logger::collectAlert('warnings', 'Ошибка! Не получилось зарегестрироваться! Проверьте еще раз ваши данные.При повторной ошибке обратитесь к администратору!');
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