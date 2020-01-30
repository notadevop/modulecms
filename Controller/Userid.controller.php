<?php 



class UserIdentificatior {

	private $cjob; 
	private $auth;
	private $glob; 
	private $filter;
	private $cookies;
	private $errGen;

	private $infoGen;

	function __construct() {
		
		$this->cjob = new CookieJob();
		$this->auth = new Auth();
		$this->glob = new GlobalParams();
		$this->filter = new Filter(); 

		$this->errGen = array();


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

			debugger('Вы вышли из своего аккаунта',__METHOD__);
			
		}
		// Установить сессию 
	}

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

		$mr = $this
				->auth
				->userExist($email);

		if (!$mr) {

			$this->errGen['noprofile'] = 'Неправильные имя или пароль!';
			return;
		} 

		if (!$this->auth->userNotBlocked($email)) {

			$this->errGen['ublocked'] = 'Ошибка! Пользователь заблокирован или удален.';
			return;
		} 

		// TODO: Аккаунт активированн или нет возможно проверка 
		// по дате последнего визита или дате регистрации 

		$profile = $this->auth->login($email, $pass);

		if ($this->defineUserProfile($profile)) {

			$this->saveAuthAction($profile['useremail'], $profile['tokenHash']);

			if(REDIRECTLOGIN) {

				//header('Location: /'); // Перебрасываем отуда, откуда пришел. 
				// TODO: header('Location: /');
				debugger('Сохранил куки и перекидываю пользователя',__METHOD__);
			}

			$this->infoGen['loggedin'] = 'Вы вошли в свой аккаунт!';
			return;
		}

		$this->errGen['noprofile'] = 'Неправильные имя или пароль!';
	}

	function defineUserProfile($profile): bool {

		if (!empty($profile)) {

			define('PROFILE', $profile);
			debugger(PROFILE, __METHOD__);

			return true;
		}
		return false;
	}

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

		$ue = $this->auth->userExist($mail);
		$ub = $this->auth->userNotBlocked($mail);

		if (!$ue || !$ub) {

			$this->logout(false, true);
			return;
		} 
		
		$profile = $this
					->auth
					->authin($mail, $token);

		if ($this->defineUserProfile($profile)) {

			$this->saveAuthAction($profile['useremail'], $profile['tokenHash']);
			return;
		}

		$this->logout(false, true);		
	}

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

			$this->errGen['noprofile'] = 'Ошибка пользователя! Возможные причины: заблокирован, удален или такого пользователя не существует!';
			return;
		} 

		$metat = $this->auth->restoration($email);

		if (!empty($metat)) {

			debugger($metat,__METHOD__);
			
			$link = HOST.'/?action=restore&userid='.$metat['id'].'&confirm='.$metat['cofirm'].'&token='.$metat['token'];

			debugger('<a href="'.$link.'" target="_blank">'.$link.'</a>');
			
			$this->infoGen['emailsent'] = 'Сылка сгенерированна и письмо должно быть отправленно!';
			// TODO: Отправка емайла пользователю для восстановления пароля

		} else {

			debugger('пустой хеш!',__METHOD__);
		}
	}

	function resetPassword() {

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

			if(!$param) { return; }

			$p[$value] = $this
						->glob
						->getGlobParam($value);

			$p[$value] = $this->filtration($p[$value], $Opt);
		}

		if (count($this->errGen) > 0) { return; }

		$sp = $this->auth->verifyActivations($p['userid'],$p['token'], $p['confirm']);

		if ($sp) {

			debugger('Показать форму с вводом пароля!',__METHOD__);
		} else {

			$this->errgen['nouser'] = 'Пусто!';
		}

	}

	function __init_auth() {

		$this->authAction();
		$this->loginAction();
		$this->restoreAction();

		$this->resetPassword();

		//$this->cjob->viewCookie(['mailhash','tokenhash']);


		$this->logout(true); 
	
		debugger($this->errGen,__METHOD__);
		debugger($this->infoGen,__METHOD__);
	}
}