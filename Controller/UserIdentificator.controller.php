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

		$this->pluginExecutor = function(string $category):bool {

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

			// тут описать обьект который будет работать с плагинами
			// Обработка запросов кода из плагинов, например проверка captca 
			return true;
		};

		$this->AuthParams = array(
			'future' 	=> '+2 Hours',
			'past'		=> '-2 Hours',
			'host'		=> '/',
			'domain'	=> 'localhost'
		);

		$this->errors = array();
	}

	// ----------------------------------------------

	private function filtration(string $input, array $options): string {

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

			$this->collectErrors('noprofile', 'Ошибка! Укажите правильный емайл');
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

	function logout(bool $redirect=false, bool $clean=false): bool{

		$this
			->glob
			->setGlobParam('_GET');

		if ($this->glob->isExist('logout') || $clean) { 

			$this->saveAuthAction('', '', true); // пустые емай и хеш и стираем данные
			
			if($redirect){ header( "refresh:2; url=".HOST ); }

			$this->collectErrors('logout', 'Вы вышли из своего аккаунта!');

			return true;
			
		}
		// Установить сессию 

		return false;
	}

	// ----------------------------------------------

	function loginAction():bool {

		$this
			->glob
			->setGlobParam('_POST');

		$e = $this
			->glob
			->isExist('loginmail');
		$p = $this
			->glob
			->isExist('loginpasswd');

		if(!$e || !$p) { return false; } 

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

		if (count($this->getErrors()) > 0) { return false; }

		$ue = $this
				->auth
				->userExist($email);
		$ub = $this
				->auth
				->userActivated($email);
		
		if (!$ue) {

			$this->collectErrors('noprof', 'Неправильные имя или пароль!');
			return false;
		} else if (!$ub) {

			$this->collectErrors('blockedprof', 'Ошибка! Пользователь заблокирован или не активирован.');
			return false;
		} 

		$profile = $this
					->auth
					->findUser($email, $pass);

		if (!$this->defineUserProfile($profile)) {

			$this->collectErrors('noprof', 'Неправильные имя или пароль!');
			return false;
		}
		
		$this->saveAuthAction($email, $profile['tokenHash']);

		if(REDIRECTLOGIN) {

			//header('Location: /'); // Перебрасываем отуда, откуда пришел. 
			debugger('Сохранил куки и перекидываю пользователя',__METHOD__);
			return true;
		}

		$this->collectErrors('loggedin', 'Вы вошли в свой аккаунт!');

		return true;
	}

	// ----------------------------------------------

	private function defineUserProfile($profile): bool {

		if (empty($profile)) { return false; }

		debugger($profile, __METHOD__);

		if (!defined('PROFILE')) {

			define('PROFILE', $profile);
		}

		return true;
	}

	// ----------------------------------------------

	function authAction():bool {

		$this
			->glob
			->setGlobParam('_COOKIE');

		$mail = $this
					->glob
					->isExist('mailhash');
		$token= $this
					->glob
					->isExist('tokenhash');

		if (!$mail || !$token) { return false; }

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
	
	private function saveAuthAction(string $email='',string $hash='', bool $gopast=false): void{

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

	function restoreAction():bool {

		$this
			->glob
			->setGlobParam('_POST');

		$email = $this
					->glob
					->isExist('restoremail');

		if(!$email) { return false; }

		$emailOpt = array(
			'maxSym' 	=> 100, 
			'minSym' 	=> 4, 
			'checkMail' => true
		);

		$email 	= $this
				->glob
				->getGlobParam('restoremail');

		$email 	= $this->filtration($email, $emailOpt);

		if (count($this->getErrors()) > 0) { return false; }

		$mr = $this
				->auth
				->userExist($email);
		$ms = $this
				->auth
				->userActivated($email);		

		if (!$mr || !$ms) {

			$this->collectErrors('noprof', 'Ошибка! Возможно пользователь: заблокирован, удален или не существует!');
			return false;
		} 

		$meta = $this
					->auth
					->generateActivations($email);

		if (empty($meta)) { return false ; }

		$link = HOST.'/auth/confirmRestore/?userid='.$meta['id'].'&confirm='.$meta['cofirm'].'&token='.$meta['token'];

		debugger('<a href="'.$link.'" target="_blank">'.$link.'</a>', __METHOD__);

		// TODO: Отправка емайла пользователю для восстановления пароля

		return true;
	}

	// ----------------------------------------------

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

		if (count($this->getErrors()) > 0) { return false; }

		$sp = $this
				->auth
				->verifyActivations($p['userid'],$p['token'], $p['confirm']);

		if (!$sp) {

			$this->collectErrors('notvalid', 'Ошибка параметров подтверждения пользователя!');
			return false;
		}

		// Возвращаем для вывода окна паролей
		if (empty($pass1) || empty($pass2)) { return true; }

		if ($pass1 !== $pass2) {

			$this->collectErrors('mismatch', 'Ошибка! пароли не совпадают.');
			return false;
		}

		$r = $this
				->auth
				->updateUserPassword($p['userid'], $pass1);

		if(!$r) { $this->collectErrors('updpasserr', 'Ошибка обновления пароля!'); return false; }
		
		$this->collectErrors('updpassgood', 'Пароль обновлен!');

		// TODO: Переправить пользователя на форму входа
		return true;
	}

	// добавляет нового пользователя в базу данных 
	// ----------------------------------------------

	function registerAction(): bool {

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

		if (count($this->getErrors()) > 0) { return false; }

		if ($pass1 !== $pass2) {

			$this->collectErrors('mismatch', 'Ошибка! пароли не совпадают.');
			return false;
		}

		$exist = $this
					->auth
					->userExist($email);

		if ($exist) {

			$this->collectErrors('noprof', 'Ошибка! Возможно такой пользователь уже зарегестрирован!');
			return false;
		}

		$r = $this
				->auth
				->insertNewUser($email, $pass1, $name);

		if (!$r) {

			$this->collectErrors('noregact', 'Ошибка! Не получилось зарегестрироваться! Проверьте еще раз ваши данные.');

			return false;
		}

		$meta = $this
					->auth
					->generateActivations($email);

		if (empty($meta)) { return false; }

		$link = HOST.'/?action=pwd&userid='.$meta['id'].'&confirm='.$meta['cofirm'].'&token='.$meta['token'];

		debugger('<a href="'.$link.'" target="_blank">'.$link.'</a>', __METHOD__);

		return true;
	}

	// проверяет уже посланные данные для активации пользователя
	// ----------------------------------------------

	function confirmRegisterAction(): bool {

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

		if (count($this->getErrors()) > 0) { return false; }

		$sp = $this
				->auth
				->verifyActivations($p['userid'],$p['token'], $p['confirm']);

		if (!$sp) {

			$this->collectErrors('notvalid', 'Ошибка параметров подтверждения пользователя!');
			return false;
		}

		$atatus = $this
					->auth
					->activateRegisteredUser($p['userid']);

		$good = false;

		if ($astatus) {

			$this->collectErrors('regactstatus', 'Аккаунт активирован!');
			$good = true;
		} else {

			$this->collectErrors('regactbad', 'Ошибка активации пользователя!');
		}

		// TODO: Переправить пользователя на форму входа
		return $good;

	}

	// ----------------------------------------------

	function runAuth(string $authAction='') {

		// Переменная которвя соберает всю информацию об авторизации и аутентификации пользователя

		$action = $authAction;

		$gen = function($k, $val) use (&$authMeta) {

			foreach ($authMeta as $key => $value) {
				
				$authMeta[$key] = ($k == $key) ? $val : !$val;
			}
		}; 

		$authMeta = array(
			'viewLoginForm' 		=> false, // Окно входа
			'viewRegistrationForm' 	=> false, // Форма регистрации
			'viewRestoreForm'		=> false, // поле восстановления
			'viewRestoreConfirmForm'=> false, // Форма ввода новых паролей 
			'userAuthentificated'	=> false, // Проверка на авторизованного пользователя
			'viewDefaultPage'		=> true, // по умолчанию страница для вывода инфо
		);

		$this->logout(true, false); 

		// Действия по авторизации
		// при указанном екшене который дается в pageBuilder выполняем определенные действия 
		// и если возвращается false то выводим форму указаного действия 
		// если true то страницу по умолчанию

		switch($action) {

			case'loginAction': 			
				if(!$this->loginAction()){ 			$gen('viewLoginForm',true); }
				break;
			case'restoreAction':		
				if(!$this->restoreAction()){ 		$gen('viewRestoreForm',true); }	
				break;
			case'confRestoreAction':		
				if(!$this->confirmRestoreAction()){ $gen('viewRestoreConfirmForm',true);}	
				break;
			case'registrationAction':			
				if(!$this->registerAction()){ 		$gen('viewRestoreForm',true); }	
				break;
		}

		return $authMeta;
	}
}