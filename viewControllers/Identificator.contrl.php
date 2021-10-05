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

			'transport' => array( 

				'useridValue'  => 'userid',
				'useridMaxSym' => 255,
				'useridMinSym' => 1,

				'userNameValue' => 'username',
				'userNameMaxSym' => 50,
				'userNameMinSym' => 2,

				'userEmailValue' => 'useremail',
				'userEmailMaxSym' => 50,
				'userEmailMinSym' => 7,

				'userPassword1Value' => 'userpassword1',
				'userPassword2Value' => 'userpassword2',
				'userPasswordMaxSym' => 20,
				'userPasswordMinSym' => 8,

				'userTokenHashValue' => 'token',
				'userConfirmHashValue' => 'confirm',
				'userTokenHashMaxSym' => 255,
				'userTokenHashMinSym' => 4,
			)
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

		Logger::collectAlert('warnings', LOGGEDOUT);

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

			Logger::collectAlert('warnings', LOGINDISABLED);
			return false;
		}

		$loginParams = array(

			$this->authParams['transport']['userEmailValue'] 	 => false,
			$this->authParams['transport']['userPassword1Value'] => false
		);

		$loginParams = $this->getInputParams($loginParams, '_POST');
		
		if(!$this->isNotEmpty($loginParams)) {
			return false;
		}

		$userExist = $this->users->userExist($loginParams[$this->authParams['transport']['userEmailValue']]);

		if(!$userExist) {

			Logger::collectAlert('attentions', ERREMAILWRONG);
			return false; 
		}

		$userNotBlocked = $this->auth->userActivated($loginParams[$this->authParams['transport']['userEmailValue']]);

		if (!$userNotBlocked) {

			Logger::collectAlert('attentions', USERBANNED);
			return false;
		}

		$findUser = $this->auth->findUser($loginParams[$this->authParams['transport']['userEmailValue']], $loginParams[$this->authParams['transport']['userPassword1Value']]);

		if(!$this->isNotEmpty($findUser) || !array_key_exists('userid', $findUser)) {

			Logger::collectAlert('attentions', ERREMAILWRONG);
			return false;
		}

		// тут получаем хеш, если нету то генерируется новый 

		$findUser['tokenHash'] = $this->auth->updateUserHash($findUser['userid'], false);

		if(!$this->isNotEmpty($findUser['tokenHash'])) {

			Logger::collectAlert('attentions', ERRGENHASH);
			return false;
		}

		$isItSaved = $this->saveAuthAction($findUser['useremail'], $findUser['tokenHash'], false, true);

		if(!$isItSaved) {

			Logger::collectAlert('attentions', ERRSAVEMETA);
			return false;
		} 

		if(defined('REDIRECTLOGIN') && REDIRECTLOGIN['redirectuser']) {

			$redirect = REDIRECTLOGIN;

			$redirect['redirectpath'] = str_replace('%userid%', $findUser['userid'], $redirect['redirectpath']);

			if($redirect['timeout'] > 0) {
				header('refresh: '.$redirect['timeout'].'; url='.$redirect['redirectpath']);
			} else {
				header('Location: '.$redirect['redirectpath']);
			}
		}

		Logger::collectAlert('success', LOGINSUCCESS);

		return true;
	}


	private function saveAuthAction(string $email, string $hash, bool $goPast=false, bool $showerr=DEBUG): bool {

		$authParams = array(

			$this->authParams['transport']['userEmailValue'] 	 => $email,
			$this->authParams['transport']['userTokenHashValue'] => $hash 
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

			Logger::collectAlert('attentions', AUTHDISABLED);
			return $this->setUserProfile(false);
		}

		$authParam = array(
			$this->authParams['transport']['userEmailValue'] 	 => false,
			$this->authParams['transport']['userTokenHashValue'] => false
		);

		$authParam = $this->getInputParams($authParam, '_COOKIE');

		if(!$this->isNotEmpty($authParam)) {
			return $this->setUserProfile(false);
		}

		$userExist = $this->users->userExist($authParam[$this->authParams['transport']['userEmailValue']]);

		if(!$userExist) { return $this->setUserProfile(false); }

		$userNotBlocked = $this->auth->userActivated($authParam[$this->authParams['transport']['userEmailValue']]);

		if (!$userNotBlocked) { return $this->setUserProfile(false); }

		$findUser = $this->auth->authUser($authParam[$this->authParams['transport']['userEmailValue']], $authParam[$this->authParams['transport']['userTokenHashValue']]);

		if(!$this->isNotEmpty($findUser) || !array_key_exists('userid', $findUser)) { return $this->setUserProfile(false); }

		// Тут обновляем хеш, если закончилось время

		$findUser['tokenhash'] = $this->auth->updateUserHash($findUser['userid'], false);

		if(!$this->isNotEmpty($findUser['tokenhash'])) { return $this->setUserProfile(false); }

		$isItSaved = $this->saveAuthAction($findUser['useremail'], $findUser['tokenhash'], false, true);

		if(!$isItSaved) { return $this->setUserProfile(false); } 

		$this->setUserProfile($findUser);

		return true;
	}


	// Тут нужно установить брать данные из параметров метода, а не с _POST 

	function restoreAction(): ?string {

		if(!defined('RESTOREALLOW') || !RESTOREALLOW ) {

			Logger::collectAlert('attentions', RESTOREDISABLED);
			return false;
		}

		$restoreParams = array(

			$this->authParams['transport']['userEmailValue'] => false
		);

		$restoreParams = $this->getInputParams($restoreParams, '_POST');

		if(!$this->isNotEmpty($restoreParams)) {
			return false;
		}

		$userExist = $this->users->userExist($restoreParams[$this->authParams['transport']['userEmailValue']]);

		if(!$userExist) {

			Logger::collectAlert('attentions', USERNOTFOUND);
			return false; 
		}

		$userNotBlocked = $this->auth->userActivated($restoreParams[$this->authParams['transport']['userEmailValue']]);

		if (!$userNotBlocked) {

			Logger::collectAlert('attentions', USERBANNED);
			return false;
		}

		$genResult = $this->auth->generateActivations($restoreParams[$this->authParams['transport']['userEmailValue']]);

		if(!$genResult) {

			Logger::collectAlert('attentions', ERRGENHASH);
			return false;
		}

		// TODO: Отправка емайла пользователю для восстановления пароля
		// TODO: сделать генерацию ссылок

		$link = HOST.'/verifyrestorerequest/?userid=' . $genResult['id'] . '&'.$this->authParams['transport']['userConfirmHashValue'].'=' . $genResult['cofirm'] . '&'.$this->authParams['transport']['userTokenHashValue'].'=' . $genResult['token'];

		Logger::collectAlert('information', $link);	

		return true;
	}


	function verifyUserActivation(): ?array {

		if(!defined('RESTOREALLOW') || !RESTOREALLOW ) {

			Logger::collectAlert('attentions', RESTOREDISABLED);
			return null;
		}

		$restoreParams = array(

			$this->authParams['transport']['useridValue'] 			=> false,
			$this->authParams['transport']['userConfirmHashValue']  => false,
			$this->authParams['transport']['userTokenHashValue'] 	=> false,
		);

		$restoreParams = $this->getInputParams($restoreParams, '_GET');
		
		if(!$this->isNotEmpty($restoreParams)) {
			return null;
		}

		if(!$this->auth->verifyActivations($restoreParams[$this->authParams['transport']['useridValue']], $restoreParams[$this->authParams['transport']['userTokenHashValue']],$restoreParams[$this->authParams['transport']['userConfirmHashValue']] )) {

			Logger::collectAlert('attentions', AUTHPARAMSERR);
			return null;
		}

		return array(
			$this->authParams['transport']['useridValue'] 			=> $restoreParams['userid'],
			$this->authParams['transport']['userConfirmHashValue'] 	=> $restoreParams['token'],
			$this->authParams['transport']['userConfirmHashValue'] 	=> $restoreParams['confirm']
		);
	}

	// verifybyid ключ используется для восстановления ключя, без должен использоваться 


	function updateUserPassword(bool $verifbyid=true, int $userid=0): bool {

		if(!defined('RESTOREALLOW') || !RESTOREALLOW ) {

			Logger::collectAlert('attentions', RESTOREDISABLED);
			return false;
		}

		// Это условие удалить???

		if ($verifbyid) {
			$verified = $this->verifyUserActivation();
			if (!$this->isNotEmpty($verified)) { 
				Logger::collectAlert('attentions', VERIFYNOTFOUND);
				return false;
			}
		}

		$updateParams = array(
			$this->authParams['transport']['userPassword1Value'] = false,
			$this->authParams['transport']['userPassword2Value'] = false
		);

		$updateParams = $this->getInputParams($updateParams, '_POST');

		if(!$this->isNotEmpty($updateParams)) {
			return false;
		}

		if ($updateParams[$this->authParams['transport']['userPassword1Value']] !== $updateParams[$this->authParams['transport']['userPassword2Value']]) {
			Logger::collectAlert('attentions', PWDNOTMATCH);
			return false;
		}

		$result = $this->users->updateUserPassword($verified['userid'], $updateParams[$this->authParams['transport']['userPassword2Value']], true);

		if (!$result) {
			Logger::collectAlert('attentions', PWDUPDERR);
			return false;
		}

		$r = $this->auth->clearActivations($verified['userid']);

		if (!$r) {

			Logger::collectAlert('attentions', ACTUSERERR);
		}

		Logger::collectAlert('success', PWDUPDATED);
		return true;
	}


	function registrationAction(): bool {

		if(!defined('REGISTRATIONALLOW') || !REGISTRATIONALLOW) {

			Logger::collectAlert('information', REGDISABLED);
			return false;
		}

		$registrationParams = array(
			$this->authParams['transport']['userNameValue'] => false,
			$this->authParams['transport']['userEmailValue'] => false,
			$this->authParams['transport']['userPassword1Value'] => false,
			$this->authParams['transport']['userPassword2Value'] => false
		);

		$registrationParams = $this->getInputParams($registrationParams, '_POST');

		if(!$this->isNotEmpty($registrationParams)) {
			return false;
		}

		if ($registrationParams[$this->authParams['transport']['userPassword1Value']] !== $registrationParams[$this->authParams['transport']['userPassword2Value']]) {

			Logger::collectAlert('attentions', PWDNOTMATCH);
			return false;
		}

		$userExist = $this->users->userExist($registrationParams[$this->authParams['transport']['userEmailValue']]);

		if($userExist) {
			Logger::collectAlert('attentions', USEREXIST);
			return false; 
		}

		$insert = $this->users->insertNewUser($registrationParams[$this->authParams['transport']['userEmailValue']], $registrationParams[$this->authParams['transport']['userPassword1Value']], $registrationParams[$this->authParams['transport']['userNameValue']]);

		if (!$insert) {
			Logger::collectAlert('attentions', ADDUSERERR); 
			return false;
		}

		$meta = $this->auth->generateActivations($registrationParams[$this->authParams['transport']['userEmailValue']]);

		if(!$meta) {
			Logger::collectAlert('attentions', ERRGENLINK);
			return false;
		}

		// TODO: Отправка емайла пользователю для восстановления пароля
		// TODO: сделать генерацию ссылок

		$link = HOST . '/verifreg/?'.$this->authParams['transport']['useridValue'].'=' . $meta['id'] . '&'.$this->authParams['transport']['userConfirmHashValue'].'=' . $meta['cofirm'] . '&'.$this->authParams['transport']['userTokenHashValue'].'=' . $meta['token'];

		Logger::collectAlert('information', $link);
		return true;

	}


	function verifyUserRegistration(): bool {

		if(!defined('REGISTRATIONALLOW') || !REGISTRATIONALLOW ) {

			Logger::collectAlert('information', REGDISABLED);
			return false;
		}

		$registrationConfirm = $this->verifyUserActivation();

		if(!$this->isNotEmpty($registrationConfirm)) {
			Logger::collectAlert('attentions', REGATRRERR);
			return false;
		}

		$status = $this->auth->activateRegisteredUser($registrationConfirm[$this->authParams['transport']['useridValue']]);

		if (!$status) {

			Logger::collectAlert('attentions', USERACTERR);
			return false;
		}

		$this->auth->clearActivations($registrationConfirm[$this->authParams['transport']['useridValue']]);

		Logger::collectAlert('success', USERACTIVED);
		return true;
	}


	// Функция которая возвращает данные из вне чтобы не дублировать код впихнул все сюда 

	function getInputParams(array $params, string $method, bool $silence=false): ?array {

		if(!$this->isNotEmpty($params) || !$this->isNotEmpty($method)) { 
			return null;
		}

		// Замыкание для фильтрации массива !!!! ----

		$filterOut = function($key, $silence) {

			if(!$this->glob->isExist($key)) {
				return false;
			}

			$value = $this->glob->getGlobParam($key);

			if (!$this->isNotEmpty($value)) {
				if(!$silence)
					Logger::collectAlert('attentions', EMPTYFIELDSEXIST);
				return false;
			}
			try {
				//$value = $this->mainSanitizer($value, 'encoding');
				$value = $this->mainSanitizer($value, 'magicquotes');
				$value = $this->mainSanitizer($value, 'fullspecchars');
				$value = $this->mainSanitizer($value, 'string');
				$value = $this->mainSanitizer($value, 'stripped');
				$value = $this->ejectedWords($value);
			} catch (Exception $e) {
				if(!$silence)
					Logger::collectAlert('attentions', $e->getMessage());
			}

			switch($key) {
				case $this->authParams['transport']['userEmailValue']:
					$max = $this->authParams['transport']['userEmailMaxSym'];
					$min = $this->authParams['transport']['userEmailMinSym'];
				break;
				case $this->authParams['transport']['userPassword1Value']:
				case $this->authParams['transport']['userPassword2Value']:
					$max = $this->authParams['transport']['userPasswordMaxSym'];
					$min = $this->authParams['transport']['userPasswordMinSym'];
				break;
				case $this->authParams['transport']['userTokenHashValue']:
				case $this->authParams['transport']['userConfirmHashValue']:
					$max = $this->authParams['transport']['userTokenHashMaxSym'];
					$min = $this->authParams['transport']['userTokenHashMinSym'];
				break;
				case $this->authParams['transport']['userNameValue']:
					$max = $this->authParams['transport']['userNameMaxSym'];
					$min = $this->authParams['transport']['userNameMinSym'];
				break;
				case $this->authParams['transport']['useridValue']:
					$max = $this->authParams['transport']['useridMaxSym'];
					$min = $this->authParams['transport']['useridMinSym'];
				break;
				default:
					$max = 0;
					$min = 0;
				break;
			}

			if ($this->isMoreThan($value, $max)) {
				if(!$silence)
					Logger::collectAlert('attentions', sprintf(ERRMAXSYMLIMIT, $max));
				return false;
			}

			if($this->isLessThen($value, $min)) {
				if(!$silence)
					Logger::collectAlert('attentions', sprintf(ERRMINSYMLIMIT, $min));
				return false; 
			}

			if($key == $this->authParams['transport']['userEmailValue'] &&  !$this->mainValidator($value, 'email')) {
				if(!$silence)
					Logger::collectAlert('attentions', ERRMAIL);
				return false; 
			}

			return $value;
		};

		$this->glob->setGlobParam($method);

		foreach ($params as $key => $value) {
			$params[$key] = $filterOut($key, $silence);
			if(!$params[$key]) return null;
		}

		return $params;
	}
}






























