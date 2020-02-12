<?php 



class UserIdentificatior {

	private $cjob; 
	private $auth;
	private $glob; 
	private $filter;
	private $cookies;
	private $errGen;
	private $infoGen;
	private $pluginExecutor;


	function __construct() {
		
		$this->cjob 	= new CookieJob();
		$this->auth 	= new Auth();
		$this->glob 	= new GlobalParams();
		$this->filter 	= new Filter(); 

		$this->errGen 	= array();

		$this->pluginExecutor = function($category) {

			switch($category) {
				case 'login': 			
					break;
				case 'restore': 		
					break;
				case 'registration': 	
					break;
				default: 				
					break;
			}

			// тут описать обьекто который будет работать с плагинами
			// Обработка запросов кода из плагинов, например проверка captca 
			return true;
		};

		$this->AuthParams = array(
			'future' 	=> '+2 Hours',
			'past'		=> '-2 Hours',
			'host'		=> '/',
			'domain'	=> 'localhost'
			//'loginKey' 	=> '',
			// 'passKey'  	=> '',
			// 'recovKey'
		);

		$this->errors = array();
	}

	// ----------------------------------------------

	function filtration(string $input, array $options): string {

		$this
			->filter
			->setVariables( 
				array(
				'key' => array(
					'value' 	=> $input,
					'maximum'  	=> $options['maxSym'],
					'minimum'  	=> $options['minSym'] 
					)
				)
			);

		// TODO: filtration: eazy, medium, hard
		$this
			->filter
			->cleanAttack('key', array('')); 

		if ($options['checkMail'] && !$this->filter->validator('key', 'email')) {

			$this->errGen['noprofile'] = 'Ошибка! Укажите правильный емайл';
		}

		if (!$this
				->filter
				->isNotEmpty('key')) {

			$this->errGen['nores'] = 'Недопустима пустая строка!';
		
		} else if (!$this
						->filter
						->isNotMore('key')) {
			
			$this->errGen['strLimit'] = 'Недопустимое кол-во символов! Слишком большие значение.';
		
		} else if (!$this
						->filter
						->isNotLess('key')) {

			$this->errGen['strLimit'] = 'Недопустимое кол-во символов! Слишком маленькое значение.'; 
		}

		return $this
					->filter
					->getKey('key');
	}

	// ----------------------------------------------

	function logout(bool $redirect=false, bool $clean=false) {

		$this
			->glob
			->setGlobParam('_GET');

		if ($this->glob->isExist('logout') || $clean) { 

			$email 	= '';
			$hash 	= '';
			$clean 	= true;

			$this->saveAuthAction($email, $hash, $clean);
			
			if($redirect){ header( "refresh:2; url=".HOST ); }

			$this->infoGen['logout'] = 'Вы вышли из своего аккаунта';
			
		}
		// Установить сессию 
	}

	// ----------------------------------------------

	function loginAction() {

		$this
			->glob
			->setGlobParam('_POST');

		$e = $this
			->glob
			->isExist('loginmail');
		$p = $this
			->glob
			->isExist('loginpasswd');

		if(!$e || !$p) { return; } 

		$nameOpt = array(
			'maxSym' 	=> 30, 
			'minSym' 	=> 4, 
			'checkMail' => true
		);
		
		$passOpt = array(
			'maxSym' 	=> 30, 
			'minSym' 	=> 4, 
			'checkMail' => false
		);

		$email 	= $this
					->glob
					->getGlobParam('loginmail');
		$pass 	= $this
					->glob
					->getGlobParam('loginpasswd');

		$email 	= $this->filtration($email, $nameOpt);
		$pass 	= $this->filtration($pass, $passOpt);

		if (count($this->errGen) > 0) { return; }

		$ue = $this
				->auth
				->userExist($email);
		$ub = $this
				->auth
				->userNotBlocked($email);
		

		if (!$ue) {

			$this->errGen['noprofile'] = 'Неправильные имя или пароль!';
			return;
		} else if (!$ub) {

			$this->errGen['ublocked'] = 'Ошибка! Пользователь заблокирован или не активирован.';
			return;
		} 

		$profile = $this
					->auth
					->findUser($email, $pass);

		if (!$this->defineUserProfile($profile)) {

			$this->errGen['noprofile'] = 'Неправильные имя или пароль!';
			return;
		}
		
		$this->saveAuthAction($email, $profile['tokenHash']);

		if(REDIRECTLOGIN) {

			//header('Location: /'); // Перебрасываем отуда, откуда пришел. 
			debugger('Сохранил куки и перекидываю пользователя',__METHOD__);
		}

		$this->infoGen['loggedin'] = 'Вы вошли в свой аккаунт!';
		return;
	}

	// ----------------------------------------------

	function defineUserProfile($profile): bool {

		if (empty($profile)) { return false; }

		define('PROFILE', $profile);
		debugger(PROFILE, __METHOD__);

		return true;
	}

	// ----------------------------------------------

	function authAction() {

		$this
			->glob
			->setGlobParam('_COOKIE');

		$mail = $this
					->glob
					->isExist('mailhash');
		$token= $this
					->glob
					->isExist('tokenhash');

		if (!$mail || !$token) { return; }

		$nameOpt = array(
			'maxSym' 	=> 500, 
			'minSym' 	=> 4, 
			'checkMail' => false
		);
		
		$passOpt = array(
			'maxSym' 	=> 500, 
			'minSym' 	=> 4, 
			'checkMail' => false
		);

		$mail 	= $this
						->glob
						->getGlobParam('mailhash');
		$token 	= $this
						->glob
						->getGlobParam('tokenhash');

		$mail 	= $this->filtration($mail, $nameOpt);
		$token 	= $this->filtration($token, $passOpt);
		
		if (count($this->errGen) > 0) {

			$this->logout(false, true);
			return;
		}

		$ue = $this
			->auth
			->userExist($mail);
		$ub = $this
			->auth
			->userNotBlocked($mail);

		if (!$ue || !$ub) {

			$this->logout(false, true);
			return;
		} 
		
		$profile = $this
						->auth
						->authUser($mail, $token);

		if ($this->defineUserProfile($profile)) {

			$this->saveAuthAction($profile['useremail'], $profile['tokenHash']);
			return;
		}

		$this->logout(false, true);		
	}

	// Устанавливаем куки и сессию или удаляем их в зависимости от переменной $gopast
	
	function saveAuthAction(string $email='',string $hash='', bool $gopast=false): void{

		$time = !$gopast ? $this->AuthParams['future'] : $this->AuthParams['past'];

		$authParams = array(
					'mailhash' 	=> $email, 
					'tokenhash' => $hash
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

	function restoreAction() {

		$this
			->glob
			->setGlobParam('_POST');

		$email = $this
					->glob
					->isExist('restoremail');

		if(!$email) { return; }

		$emailOpt = array(
			'maxSym' 	=> 100, 
			'minSym' 	=> 4, 
			'checkMail' => true
		);

		$email 	= $this
				->glob
				->getGlobParam('restoremail');

		$email 	= $this->filtration($email, $emailOpt);

		if (count($this->errGen) > 0) { return; }

		$mr = $this
				->auth
				->userExist($email);
		$ms = $this
				->auth
				->userNotBlocked($email);		

		if (!$mr || !$ms) {

			$this->errGen['noprofile'] = 'Ошибка! Возможно пользователь: заблокирован, удален или не существует!';
			return;
		} 

		$meta = $this
					->auth
					->generateActivations($email);

		if (empty($meta)) { return; }

		$link = HOST.'/?action=pwd&userid='.$meta['id'].'&confirm='.$meta['cofirm'].'&token='.$meta['token'];

		debugger('<a href="'.$link.'" target="_blank">'.$link.'</a>', __METHOD__);

		// TODO: Отправка емайла пользователю для восстановления пароля
	}

	// ----------------------------------------------

	// Должен вернуть true | false 

	function confirmRestoreAction(): bool {

		$this
			->glob
			->setGlobParam('_GET');

		$params = array('userid','confirm', 'token');

		foreach ($params as $key => $value) {
			
			$Opt = array(
				'maxSym' 	=> 50, 
				'minSym' 	=> $value == 'userid' ? 1 : 30, 
				'checkMail' => false
			);

			$param = $this
					->glob
					->isExist($value);

			if(!$param) { return false; }

			$p[$value] = $this
						->glob
						->getGlobParam($value);

			$p[$value] = $this->filtration($p[$value], $Opt);
		}

		$getpass = function($param) {

			$this
				->glob
				->setGlobParam('_POST');

			if (!$this
					->glob
					->isExist($param)) { return; }

			$Opt = array(
				'maxSym' 	=> 100, 
				'minSym' 	=> 6, 
				'checkMail' => false
			);

			$pass1 	= $this
						->glob
						->getGlobParam($param);

			return $this->filtration($pass1, $Opt);
		};

		$pass1 = $getpass('restorepwd1');
		$pass2 = $getpass('restorepwd2');

		if (count($this->errGen) > 0) { return false; }

		$sp = $this
				->auth
				->verifyActivations($p['userid'],$p['token'], $p['confirm']);

		if (!$sp) {

			$this->errGen['notvalid'] = 'Ошибка параметров подтверждения пользователя!';
			return false;
		}

		// Возвращаем для вывода окна паролей
		if (empty($pass1) || empty($pass2)) { return true; }

		if ($pass1 !== $pass2) {

			$this->errGen['mismatch'] = 'Ошибка! пароли не совпадают.';
			return false;
		}

		$r = $this
				->auth
				->updateLoginPassword($p['userid'], $pass1);

		if(!$r) { $this->errGen['Ошибка обновления пароля!']; return false; }
		
		$this->infoGen['updatepwd'] = 'Пароль обновлен!';

		// TODO: Переправить пользователя на форму входа
		return true;
	}

	// добавляет нового пользователя в базу данных 
	// ----------------------------------------------

	function registerAction() {

		$this
			->glob
			->setGlobParam('_POST');

		$email = $this
			->glob
			->isExist('userregemail');
		$name = $this
			->glob
			->isExist('userregname');

		$pass1 = $this
			->glob
			->isExist('userregpassword1');

		$pass2 = $this
			->glob
			->isExist('userregpassword2');

		if(!$email || !$name || !$pass1 || !$pass2) { return false; } 

		$email 	= $this
					->glob
					->getGlobParam('userregemail');
		$name 	= $this
					->glob
					->getGlobParam('userregname');
		$pass1 	= $this
					->glob
					->getGlobParam('userregpassword1');
		$pass2 	= $this
					->glob
					->getGlobParam('userregpassword2');

		$emailOpt = array(
			'maxSym' 	=> 40, 
			'minSym' 	=> 6, 
			'checkMail' => true
		);

		$nameOpt = array(
			'maxSym' 	=> 30, 
			'minSym' 	=> 4, 
			'checkMail' => false
		);

		$passOpt = array(
			'maxSym' 	=> 35, 
			'minSym' 	=> 6, 
			'checkMail' => false
		);

		$email 	= $this->filtration($email, $emailOpt);
		$name 	= $this->filtration($name, $nameOpt);
		$pass1 	= $this->filtration($pass1, $passOpt);
		$pass2 	= $this->filtration($pass2, $passOpt);

		if (count($this->errGen) > 0) { return false; }

		if ($pass1 !== $pass2) {

			$this->errGen['mismatch'] = 'Ошибка! пароли не совпадают.';
			return false;
		}

		$exist = $this
					->auth
					->userExist($email);

		if ($exist) {

			$this->errGen['noprofile'] = 'Ошибка! Возможно такой пользователь уже зарегестрирован!';
			return false;
		}

		$r = $this
				->auth
				->insertNewUser($email, $pass1, $name);

		if (!$r) {

			$this->errGen['noregistration'] = 'Ошибка! Не получилось зарегестрироваться! Проверьте еще раз ваши данные.';
			return false;
		}

		$meta = $this
			->auth
			->generateActivations($email);

		if (empty($meta)) { 

			debugger('meta',__METHOD__);
			return; }

		$link = HOST.'/?action=pwd&userid='.$meta['id'].'&confirm='.$meta['cofirm'].'&token='.$meta['token'];

		debugger('<a href="'.$link.'" target="_blank">'.$link.'</a>', __METHOD__);

	}

	// проверяет уже посланные данные для активации пользователя
	// ----------------------------------------------

	function confirmRegisterAction() {

		// Добавляем последний визит, привелегии пользователю

		$this
			->glob
			->setGlobParam('_GET');

		$params = array('userid','confirm', 'token');

		foreach ($params as $key => $value) {
			
			$Opt = array(
				'maxSym' 	=> 50, 
				'minSym' 	=> $value == 'userid' ? 1 : 30, 
				'checkMail' => false
			);

			$param = $this
					->glob
					->isExist($value);

			if(!$param) { return false; }

			$p[$value] = $this
						->glob
						->getGlobParam($value);

			$p[$value] = $this->filtration($p[$value], $Opt);
		}

		$getpass = function($param) {

			$this
				->glob
				->setGlobParam('_POST');

			if (!$this
					->glob
					->isExist($param)) { return; }

			$Opt = array(
				'maxSym' 	=> 100, 
				'minSym' 	=> 6, 
				'checkMail' => false
			);

			$pass1 	= $this
						->glob
						->getGlobParam($param);

			return $this->filtration($pass1, $Opt);
		};

		$pass1 = $getpass('restorepwd1');
		$pass2 = $getpass('restorepwd2');

		if (count($this->errGen) > 0) { return false; }

		$sp = $this
				->auth
				->verifyActivations($p['userid'],$p['token'], $p['confirm']);

		if (!$sp) {

			$this->errGen['notvalid'] = 'Ошибка параметров подтверждения пользователя!';
			return false;
		}

		$atatus = $this
						->auth
						->activateRegisteredUser($p['userid']);

		$good = false;

		if ($astatus) {

			$this->infoGen['regactivated'] = 'Аккаунт активирован!';
			$good = true;
		} else {

			$this->errGen['regactivated'] = 'Ошибка активации пользователя!';
		}


		// TODO: Переправить пользователя на форму входа
		return $good;

	}

	// ----------------------------------------------

	function __init_auth() {

		// Переменная которвя соберает всю информацию об авторизации и аутентификации пользователя
		$authStatus = array();

		// Авторизация и Аутентификация отрабатывают в любой части кода
		$this->authAction();
		$this->loginAction();

		// Восстановление только по путям _GET
		$this->restoreAction();
		$this->confirmRestoreAction();

		$this->registerAction();

		
		// Регистрация только по путям _GET 

		$this->logout(true); 

		debugger($this->errGen,__METHOD__);
		debugger($this->infoGen,__METHOD__);

		//$this
		//	->cjob
		//	->viewCookie(['mailhash','tokenhash']);
	}
}