<?php

class UserIdentificator extends Errors {

	private $cjob;
	private $auth;
	private $glob;
	private $filter;
	private $cookies;
	private $pluginExecutor;

	function __construct() {

		parent::__construct();

		$this->cjob 	= new CookieJob();
		$this->auth 	= new Auth();
		$this->glob 	= new GlobalParams();
		$this->filter 	= new Filter();

		// Временно, удалить поже 

		$this->pluginExecutor = function (string $category): bool {

			switch ($category) {
				case 'login':		break;
				case 'restore':		break;
				case 'registration':break;
				default:			break;
			}

			// тут описать обьект который будет работать с плагинами
			// Обработка запросов кода из плагинов, например проверка captca
			return true;
		};

		$this->AuthParams = array(
			'future' => '+2 Hours',
			'past' => '-2 Hours',
			'host' => '/',
			'domain' => 'localhost',
		);

		$this->errors = array();
	}

	function test($param) {

		debugger('This is result of method param: '.$param);
	}

	// ----------------------------------------------

	private function filtration(string $input, array $options): string{

		$this
			->filter
			->setVariables(
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

			$this->collectErrors('noprofile', 'Ошибка! Укажите правильный емайл');
		}

		// конвертирует в целое число 
		if(isset($options['getNumber']) && $options['getNumber']){

			$this
				->filter
				->convertToNumber('key');
		}

		if (!$this->filter->isNotEmpty('key')) {

			$this->collectErrors('strWrong', 'Недопустима пустая строка!');

		} else if (!$this->filter->isNotMore('key')) {

			$this->collectErrors('strLimit', 'Недопустимое кол-во символов! Большое значение.');

		} else if (!$this->filter->isNotLess('key')) {

			$this->collectErrors('strLimit', 'Недопустимое кол-во символов! Маленькое значение.');
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

			$this->saveAuthAction('', '', true); // пустые емай и хеш и стираем данные

			if ($redirect) { header("refresh:5; url=" . HOST); }

			$this->collectErrors('logout', 'Вы вышли из своего аккаунта!');

			return true;

		}
		// Установить сессию

		return false;
	}

	// ----------------------------------------------

	function loginAction(): bool{

		$this
			->glob
			->setGlobParam('_POST');

		$e = $this
			->glob
			->isExist('loginmail');
		$p = $this
			->glob
			->isExist('loginpasswd');

		if (!$e || !$p) {return false;}

		$nameOpt = array(
			'maxSym' => 30,
			'minSym' => 4,
			'checkMail' => true,
		);

		$passOpt = array(
			'maxSym' => 30,
			'minSym' => 4,
			'checkMail' => false,
		);

		$email = $this
			->glob
			->getGlobParam('loginmail');
		$pass = $this
			->glob
			->getGlobParam('loginpasswd');

		$email = $this->filtration($email, $nameOpt);
		$pass = $this->filtration($pass, $passOpt);

		if (count($this->getErrors()) > 0) {return false;}

		$ue = $this
			->auth
			->userExist($email);
		$ub = $this
			->auth
			->userActivated($email);

		if (!$ue) {

			$this->collectErrors('wrongpass', 'Неправильные имя или пароль!');
			return false;
		} else if (!$ub) {

			$this->collectErrors('blocked', 'Ошибка! Пользователь заблокирован или не активирован.');
			return false;
		}

		$profile = $this
			->auth
			->findUser($email, $pass);

		if (!$this->defineUserProfile($profile)) {

			$this->collectErrors('wrongpass', 'Неправильные имя или пароль!');
			return false;
		}

		$this->saveAuthAction($email, $profile['tokenHash']);

		if (REDIRECTLOGIN) {

			//header('Location: /'); // Перебрасываем отуда, откуда пришел.
			debugger('Сохранил куки и перекидываю пользователя', __METHOD__);
			return true;
		}

		$this->collectErrors('loggedin', 'Вы вошли в свой аккаунт!');

		return true;
	}

	// ----------------------------------------------

	private function defineUserProfile($profile): bool {

		if (empty($profile)) {return false;}

		// Добавить определение привелегий пользователя 

		if (!defined('PROFILE')) {define('PROFILE', $profile);}

		return true;
	}

	// ----------------------------------------------

	function authAction(): bool {

		$this
			->glob
			->setGlobParam('_COOKIE');

		$mail = $this
			->glob
			->isExist('mailhash');
		$token = $this
			->glob
			->isExist('tokenhash');

		if (!$mail || !$token) {return false;}

		$nameOpt = array(
			'maxSym' => 500,
			'minSym' => 4,
			'checkMail' => false,
		);

		$passOpt = array(
			'maxSym' => 500,
			'minSym' => 4,
			'checkMail' => false,
		);

		$mail = $this
			->glob
			->getGlobParam('mailhash');
		$token = $this
			->glob
			->getGlobParam('tokenhash');

		$mail = $this->filtration($mail, $nameOpt);
		$token = $this->filtration($token, $passOpt);

		if (count($this->getErrors()) > 0) {

			$this->logout(false, true);
			return false;
		}

		$ue = $this
			->auth
			->userExist($mail);
		$ub = $this
			->auth
			->userActivated($mail);

		if (!$ue || !$ub) {

			$this->logout(false, true);
			return false;
		}

		$profile = $this
			->auth
			->authUser($mail, $token);

		if ($this->defineUserProfile($profile)) {

			$this->saveAuthAction($profile['useremail'], $profile['tokenHash']);
			return true;
		}

		$this->logout(false, true);
		return false;
	}

	// Устанавливаем куки и сессию или удаляем их в зависимости от переменной $gopast

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


	// Временно !!!

	function build_query(array $params): string {

		return http_build_query($params);
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

		if (count($this->getErrors()) > 0) {return null;}

		$mr = $this
			->auth
			->userExist($email);
		$ms = $this
			->auth
			->userActivated($email);

		if (!$mr || !$ms) {

			$this->collectErrors('banned', 'Ошибка! Возможно пользователь: заблокирован, удален или не существует!');
			return null;
		}

		$meta = $this
			->auth
			->generateActivations($email);

		if (empty($meta)) {return null;}

		// TODO: Отправка емайла пользователю для восстановления пароля
		// TODO: сделать генерацию ссылок

		return HOST . '/verifres/?userid=' . $meta['id'] . '&confirm=' . $meta['cofirm'] . '&token=' . $meta['token'];
	}

	/**
	*	Метод обновления пароля при условии, если все параметры правильные  

	*   если $verifbyid установлен в false  то нужно указать по $id 
	*/

	function updatePassword(bool $verifbyid=true): bool {

		if ($verifbyid) {

			//$this->collectErrors('nopass', 'Ошибка! укажите пароли для обновления!');

			$p = $this->verifyUserModifications();

			if (!$p || empty($p)) {return false;}
		}

		$this
			->glob
			->setGlobParam('_POST');

		$getpass = function ($param) {

			if (!$this
					->glob
					->isExist($param)) {return false;}

			$Opt = array(
				'maxSym' 	=> 100,
				'minSym' 	=> 6,
				'checkMail' => false,
			);

			$value = $this
						->glob
						->getGlobParam($param);

			return $this->filtration($value, $Opt);
		};

		$pass = array('newpassword1', 'newpassword2');

		foreach ($pass as $key => $value) {

			$pass[$key] = $getpass($value);
		}

		if (count($this->getErrors()) > 0) { return false;}

		if ($pass[0] !== $pass[1]) {

			$this->collectErrors('mismatch', 'Ошибка! пароли не совпадают.');
			return false;
		}

		//var_dump($pass);

		$r = $this
				->auth
				->updateUserPassword($p['userid'], $pass[0]);

		if (!$r) {

			$this->collectErrors('updpasserr', 'Ошибка обновления пароля!');
			return false;
		}

		$this->collectErrors('updpassgood', 'Пароль обновлен!');

		// TODO: Переправить пользователя на форму входа
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

			if(!$this->glob->isExist($value)) { return null; }

			$p[$value] = $this
							->glob
							->getGlobParam($value);

			$p[$value] = $this->filtration($p[$value], $Opt);
		}

		if (count($this->getErrors()) > 0) {

			$this->collectErrors('strLimit', 'Ошибка параметров подтверждения пользователя!');
			return null;
		}

		$sp = $this
				->auth
				->verifyActivations($p['userid'], $p['token'], $p['confirm']);

		if (!$sp) {

			$this->collectErrors('notvalid', 'Ошибка параметров подтверждения пользователя!');
			return null;
		}

		return $p;

		// TODO: Переправить пользователя на форму входа
	}

	// добавляет нового пользователя в базу данных
	// ----------------------------------------------

	function regAction():  ? string{

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

		if (count($this->getErrors()) > 0) {return null;}

		if ($p['userregpassword1'] !== $p['userregpassword2']) {

			$this->collectErrors('mismatch', 'Ошибка! пароли не совпадают.');
			return false;
		}

		if ($this->auth->userExist($p['userregemail'])) {

			$this->collectErrors('profilexist', 'Ошибка! Возможно такой пользователь уже зарегестрирован!');
			return null;
		}

		if (!$this->auth->insertNewUser($p['userregemail'], $p['userregpassword1'], $p['userregname'])) {

			$this->collectErrors('noregact', 'Ошибка! Не получилось зарегестрироваться! Проверьте еще раз ваши данные.При повторной ошибке обратитесь к администратору!');
			return null;
		}

		$meta = $this
			->auth
			->generateActivations($p['userregemail']);

		if (empty($meta)) {return false;}

		// TODO: Отправка емайла пользователю для восстановления пароля
		// TODO: сделать генерацию ссылок

		return HOST . '/verifreg/?action=pwd&userid=' . $meta['id'] . '&confirm=' . $meta['cofirm'] . '&token=' . $meta['token'];

	}

	function verifyRegistration() : bool{

		// Добавляем последний визит, привелегии пользователю
		// -------

		$p = $this->verifyUserModifications();

		if (!$p || empty($p)) {return false;}

		$astatus = $this
						->auth
						->activateRegisteredUser($p['userid']);

		if ($astatus) {

			$this->collectErrors('regactstatus', 'Аккаунт активирован!');
			return true;
		} 

		$this->collectErrors('regactbad', 'Ошибка активации пользователя!');

		// TODO: Переправить пользователя на форму входа
		return false;

	}
}