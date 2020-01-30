<?php 



class UserIdentificatior {

	private $cjob; 
	private $auth;
	private $glob; 
	private $filter;
	private $cookies;
	private $errGen;

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

		$profile = $this->auth->login($email, $pass);

		if ($this->defineUserProfile($profile)) {

			$this->saveAuthAction($profile['useremail'], $profile['tokenHash']);

			if(REDIRECTLOGIN) {

				//header('Location: /'); // Перебрасываем отуда, откуда пришел. 
				debugger('Сохранил куки и перекидываю пользователя',__METHOD__);
			}
			return;
		}

		$this->errGen['noprofile'] = 'Неправильные имя или пароль!';

		// TODO: header('Location: /');
		
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

		$mailhash 	= $this
						->glob
						->getGlobParam('mailhash');
		$tokenhash 	= $this
						->glob
						->getGlobParam('tokenhash');

		$mailhash 	= $this->filtration($mailhash, $nameOpt);
		$tokenhash 	= $this->filtration($tokenhash, $passOpt);
		
		if (count($this->errGen) > 0) {

			$this->logout(false, true);
			return;
		}

		if (!$this->auth->userExist($mailhash) || !$this->auth->userNotBlocked($mailhash)) {

			$this->logout(false, true);
			return;
		} 
		
		$profile = $this
					->auth
					->authin($mailhash, $tokenhash);

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

		$hash = $this->auth->restoration($email);

		if (!empty($hash)) {

			$link = HOST.'/?action=restore&user='.$email.'&confirm='.$hash;

			debugger('<a href="'.$link.'" target="_blank">'.$link.'</a>');
		} else {
			debugger('пустой хеш!',__METHOD__);
		}
	}

	function __init_auth() {

		$this->authAction();
		$this->loginAction();
		$this->restoreAction();

		$this->logout(true); 
	
		debugger($this->errGen,__METHOD__);
	}
}