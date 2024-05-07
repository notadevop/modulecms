<?php

// Класс идентификации пользователя

class Identificator extends Filter {

	// Параметры для правильного пути отслеживания

	const USERIDVALUE 	= 'userid';
	const USERNAMEVALUE = 'username';
	const USERMAILVALUE = 'useremail';
	const USERPWD1VALUE = 'userpassword1';
	const USERPWD2VALUE = 'userpassword2';
	const TOKENHSHVALUE = 'token';
	const CONFRHSHVALUE = 'confirm';
	const USERALIEN		= 'alienuser';


	// Ссылки на пути

	const REGLINK = '';

	private $authSts;


	// ключ который определяет где искать токен

	const CSRFKEY 		= 'authCsrfToken';
	const CSRFVALUE 	= 'isthatu';

	// POST keys for forms buttons

	const LOGINBKEY = 'loginAction';
	const REGISBKEY = 'RegistrationAction';
	const RESTORBKEY= 'RestoreAction';
	const UPDPWDBKEY= 'UpdatePwdAction';


	function __construct() {


		$this->authParams = array(

			// Для куки параметры по умолчанию

			'future' 	=> '+2 Hours',
			'past' 		=> '-2 Hours',
			'host' 		=> '/',
			'domain' 	=> HOST,

			// Использовать из базы сохраненные параметры !!!

			// Данные при входе, так же для регистрации, и восстановления пароля

			'transport' => array(

				'useridMaxSym' 			=> 255,
				'useridMinSym' 			=> 1,

				'userNameMaxSym' 		=> 50,
				'userNameMinSym' 		=> 2,

				'userEmailMaxSym' 		=> 50,
				'userEmailMinSym' 		=> 7,

				'userPasswordMaxSym' 	=> 20,
				'userPasswordMinSym' 	=> 8,

				'userTokenHashMaxSym' 	=> 255,
				'userTokenHashMinSym' 	=> 4,

				'csrfTokenMaxSym' 		=> 255,
				'csrfTokenMinSym' 		=> 10,
			)
			// Данные с проверки подтверждения авторизации
		);



		$this->auth 	= new Auth();
		$this->glob 	= new GlobalParams();
		$this->users 	= new Users();
		$this->granter 	= new PrivelegesController();
		$this->cookie 	= new Cookie();

		// Переменные для отключения авторизации и регистрации
		// В зависимости от тех параметров которые в системе
		// и установленны пользователем

		$this->authSts = array(

			'login_status'  => LOGINALLOW,
			'auth_status' 	=> AUTHALLOW,
			'reg_status'  	=> REGISTRATIONALLOW,
			'restore_status'=> RESTOREALLOW,
		);

		$stg 		= new HostSettings();
		$keys 	= $stg->getSettings($this->authSts);

		foreach ($keys as $key => $value) {

			$this->authSts[$key] = (!$value || !$this->authSts[$key]) ? false : true;
		}
	}

	private $auth;
	private $glob;
	private $users;
	private $granter;
	private $authParams;
	private $cookie;


	// переменная это принудительный выход из системы, если даже нету _GET => logout параметра!

	function logout(bool $redirect=false, bool $permQuit=false): array {

		$pageTitle = array(
			'pageTitle' 	=> 'Окно выхода!',
			'successfull'	=> false,
		);

		$this->glob->setGlobParam('_GET');

		if(!$this->glob->isExist('logout') && !$permQuit) { return $pageTitle; }

		// Установить Csrf для проверки пользователь это сделал или нет
		//$loginParams = $this->getInputParams($loginParams, '_GET');

		$authParam = array(
			self::USERMAILVALUE => '',
			self::TOKENHSHVALUE => '',
		);

		foreach ($authParam as $key => $value) {

			$this->cookie->setName($key);
			$this->cookie->setDomain($this->authParams['domain']);
			$this->cookie->setPath($this->authParams['host']);
			$this->cookie->delete();
		}

		Csrf::removeToken(self::CSRFKEY);

		Logger::collectAlert(Logger::SUCCESS, LOGGEDOUT);

		$pageTitle['successfull'] = true;

		if(!$redirect || !defined('LOGOUTALLOW')) {
			return $pageTitle;
		}

		if(LOGOUTALLOW) {

			if (LOGOUTREDIRTIMEOUT > 0) {
				header('refresh:'.LOGOUTREDIRTIMEOUT.'; url='. LOGOUTREDIRPATH);
			} else {
				header('Location: '. LOGOUTREDIRPATH);
			}
		}

		return $pageTitle;
	}




	// МЕТОД ДЛЯ ВХОДА В СИСТЕМУ ------------------------

	function loginAction(): array {

		$pageTitle = array(

			'pageTitle' 	=> 'Окно входа', // TODO: <--- Cперва нужно исправить приоритеты загрузок через пути
			'successfull'	=> false,
		);


		if(!$this->authSts['login_status']) {

			Logger::collectAlert(Logger::INFORMATION, LOGINDISABLED);
			return $pageTitle;
		}

		$loginParams = array(

			self::USERMAILVALUE => false,
			self::USERPWD1VALUE => false,
			//self::USERALIEN 	=> false,
			self::CSRFVALUE 	=> false,
		);

		$loginParams = $this->getInputParams($loginParams, '_POST');

		if(!$this->isNotEmpty($loginParams)) {
			return $pageTitle;
		}

		// Временно установленно

		if (!Csrf::verifyToken(self::CSRFKEY, true, $loginParams[self::CSRFVALUE])) {

			Logger::collectAlert(Logger::ATTENTIONS, CSRFUNSUCCESSFULL);
			//Csrf::removeToken(self::CSRFKEY);
			return $pageTitle;
		}

		$userExist = $this->users->userExist($loginParams[self::USERMAILVALUE]);

		if(!$userExist) {

			Logger::collectAlert(Logger::ATTENTIONS, ERREMAILWRONG);
			return $pageTitle;
		}

		$userNotBlocked = $this->auth->userActivated($loginParams[self::USERMAILVALUE]);

		if($this->auth->didYouActivated()) {

			Logger::collectAlert(Logger::ATTENTIONS, USERNOTCONFREG);
			return $pageTitle;
		}

		if (!$userNotBlocked) {

			Logger::collectAlert(Logger::ATTENTIONS, USERBANNED);
			return $pageTitle;
		}

		$findUser = $this->auth->findUser($loginParams[self::USERMAILVALUE], $loginParams[self::USERPWD1VALUE]);

		if(!$this->isNotEmpty($findUser) || !array_key_exists(self::USERIDVALUE, $findUser)) {

			Logger::collectAlert(Logger::ATTENTIONS, ERREMAILWRONG);
			return $pageTitle;
		}

		// тут получаем хеш, если нету то генерируется новый

		$findUser['tokenHash'] = $this->auth->updateUserHash($findUser['userid'], false);

		if(!$this->isNotEmpty($findUser['tokenHash'])) {

			Logger::collectAlert(Logger::ATTENTIONS, ERRGENHASH);
			return $pageTitle;
		}

		// Тут нужно установить долгое время или нет
		// $this->authParams['future'] = '+48 Hours',

		//if($loginParams[self::USERALIEN]) {
		//	 $this->authParams['future'] = '+48 Hours';
		//}


		$params = array(

			self::USERMAILVALUE => $findUser['useremail'],
			self::TOKENHSHVALUE => $findUser['tokenHash'],
		);

		foreach ($params as $key => $value) {

			$this->cookie->setName($key);
			$this->cookie->setDomain($this->authParams['domain']);
			$this->cookie->setPath($this->authParams['host']);

			$this->cookie->setValue($value);
			$this->cookie->setTime($this->authParams['future']);

			if (!$this->cookie->create()) {
				Logger::collectAlert(Logger::ATTENTIONS, ERRSAVEMETA);
				return $pageTitle;
			}
		}


		if(LOGINREDIRECT) {
			$source = Router::getRoute('/profile/:num');
			$path = str_replace(':num', $findUser['userid'] ,$source['url']);

			if(LOGINREDIRTIMEOUT > 0) {
				header('refresh: '.LOGINREDIRTIMEOUT.'; url='.$path);
			} else {
				header('Location: '.$path);
			}
		}

		Logger::collectAlert(Logger::SUCCESS, LOGINSUCCESS);

		Csrf::removeToken(self::CSRFKEY);

		$pageTitle['successfull'] = true;

		return $pageTitle;
	}


	function AuthAction($profile = null): bool {

		$initUser = function ($profile) {

			$initialized = false;

			// По умолчанию пустой массив так, как пользователя нету

			$user = array(
				'userid' 	=> 0,
				'username' 	=> null,
				'useremail' => null,
				'priveleges'=> null,
			);

			if(!empty($profile) && array_key_exists('userid', $profile)) {

				foreach ($profile as $key => $value) {

					$user[$key] = $value;
				}

				$this->granter->initUser($user['userid']);

				$perms = $this->granter->getPermsOfUser();

				if ($this->isNotEmpty($perms)) {
					$user['priveleges'] = implode(', ', $perms);
				}

				$initialized = true;
			}

			if(!defined('PROFILE'))
				define('PROFILE',$user);

			return $initialized;
		};

		if(!$this->authSts['auth_status']) {
			return $initUser(false);
		}

		$authParam = array(
			self::USERMAILVALUE => false,
			self::TOKENHSHVALUE => false,
		);

		$authParam = $this->getInputParams($authParam, '_COOKIE');

		if(!$this->isNotEmpty($authParam)) {

			return $initUser(false);
		}

		$userExist = $this->users->userExist($authParam[self::USERMAILVALUE]);

		if(!$userExist) {
			return $initUser(false);
		}

		$userNotBlocked = $this->auth->userActivated($authParam[self::USERMAILVALUE]);

		if (!$userNotBlocked) {

			return $initUser(false);
		}

		$findUser = $this->auth->authUser($authParam[self::USERMAILVALUE], $authParam[self::TOKENHSHVALUE]);

		if(!$this->isNotEmpty($findUser) || !array_key_exists(self::USERIDVALUE, $findUser)) {

			return $initUser(false);
		}

		// Тут обновляем хеш, если закончилось время

		$findUser['tokenhash'] = $this->auth->updateUserHash($findUser['userid'], false);

		if(!$this->isNotEmpty($findUser['tokenhash'])) {

			return $initUser(false);
		}

		$authParam[self::USERMAILVALUE] = $findUser['useremail'];
		$authParam[self::TOKENHSHVALUE] = $findUser['tokenhash'];

		foreach ($authParam as $key => $value) {

			$this->cookie->setName($key);
			$this->cookie->setDomain($this->authParams['domain']);
			$this->cookie->setPath($this->authParams['host']);

			$this->cookie->setValue($value);
			$this->cookie->setTime($this->authParams['future']);

			if (!$this->cookie->create()) {

				return $initUser(false);
			}
		}

		return $initUser($findUser);
	}


	// Тут нужно установить брать данные из параметров метода, а не с _POST

	function restoreAction(): array {

		$pageTitle = array(

			'pageTitle' 	=> 'Форма восстановления пароля',
			'successfull'	=> false,
		);

		if(!$this->authSts['restore_status']) {
			Logger::collectAlert(Logger::ATTENTIONS, RESTOREDISABLED);
			return $pageTitle;
		}

		$restoreParams = array(
			self::USERMAILVALUE => false,
			self::CSRFVALUE 	=> false,
		);

		$restoreParams = $this->getInputParams($restoreParams, '_POST');

		if(!$this->isNotEmpty($restoreParams)) {
			return $pageTitle;
		}

		// Временно установленно

		if (!Csrf::verifyToken(self::CSRFKEY, false, $restoreParams[self::CSRFVALUE])) {
			Logger::collectAlert(Logger::ATTENTIONS, CSRFUNSUCCESSFULL);
			return $pageTitle;
		}

		$userExist = $this->users->userExist($restoreParams[self::USERMAILVALUE]);

		if(!$userExist) {
			Logger::collectAlert(Logger::ATTENTIONS, USERNOTFOUND);
			return $pageTitle;
		}

		$userNotBlocked = $this->auth->userActivated($restoreParams[self::USERMAILVALUE]);

		if($this->auth->didYouActivated()) {

			Logger::collectAlert(Logger::ATTENTIONS, USERNOTCONFREG);
			return $pageTitle;
		}

		if (!$userNotBlocked) {
			Logger::collectAlert(Logger::ATTENTIONS, USERBANNED);
			return $pageTitle;
		}

		$genResult = $this->auth->generateActivations($restoreParams[self::USERMAILVALUE]);

		if(!$genResult) {
			Logger::collectAlert(Logger::ATTENTIONS, ERRGENHASH);
			return $pageTitle;
		}

		// TODO: Отправка емайла пользователю для восстановления пароля
		// TODO: сделать генерацию ссылок

		$source = Router::getRoute('/verifyrestorerequest');

		$link = HOST.$source['url'].'/?'.self::USERIDVALUE.'=' . $genResult['id'] . '&'.self::CONFRHSHVALUE.'=' . $genResult['cofirm'] . '&'.self::TOKENHSHVALUE.'=' . $genResult['token'];

		Logger::collectAlert(Logger::INFORMATION, $link);

		$pageTitle['successfull'] = true;

		return $pageTitle;
	}


	// Метод для подтверждения регистрации пользователя

	// Тут используется внешняя ссылка id, confirm hash и token hash

	function verifyUserActivation($silence=false): ?array {

		if(!$this->authSts['restore_status']) {

			Logger::collectAlert(Logger::ATTENTIONS, RESTOREDISABLED);
			return null;
		}

		$restoreParams = array(

			self::USERIDVALUE 	=> false,
			self::CONFRHSHVALUE => false,
			self::TOKENHSHVALUE => false,
		);

		$restoreParams = $this->getInputParams($restoreParams, '_GET');

		if(!$this->isNotEmpty($restoreParams)) {
			return null;
		}

		if(!$this->auth->verifyActivations($restoreParams[self::USERIDVALUE], $restoreParams[self::TOKENHSHVALUE],$restoreParams[self::CONFRHSHVALUE] )) {

			if(!$silence) {
				Logger::collectAlert(Logger::ATTENTIONS, AUTHPARAMSERR);
			}

			return null;
		}

		return array(
			self::USERIDVALUE 		=> $restoreParams[self::USERIDVALUE],
			self::CONFRHSHVALUE 	=> $restoreParams[self::CONFRHSHVALUE],
			self::TOKENHSHVALUE 	=> $restoreParams[self::TOKENHSHVALUE],
		);
	}

	// verifybyid ключ используется для восстановления ключя, без должен использоваться


	function updateUserPassword(bool $verifbyid=true, int $userid=0): bool {

		if(!$this->authSts['restore_status']) {

			Logger::collectAlert(Logger::INFORMATION, RESTOREDISABLED);
			return false;
		}

		// TODO: ВЫДАСТ ОШИБКУ ЕСЛИ ПОСТАВИТЬ $verifiedbyid = false

		if ($verifbyid) {
			$verified = $this->verifyUserActivation(true);

			if (!$this->isNotEmpty($verified)) {
				Logger::collectAlert(Logger::ATTENTIONS, VERIFYNOTFOUND);
				return false;
			}
		}

		$updateParams = array(

			self::USERPWD1VALUE => false,
			self::USERPWD2VALUE => false,
			self::CSRFVALUE 	=> false,
		);

		$updateParams = $this->getInputParams($updateParams, '_POST');

		if(!$this->isNotEmpty($updateParams)) {
			return false;
		}

		// Временно установленно

		if (!Csrf::verifyToken(self::CSRFKEY, false, $updateParams[self::CSRFVALUE])) {
			Logger::collectAlert(Logger::ATTENTIONS, CSRFUNSUCCESSFULL);
			return false;
		}


		if ($updateParams[self::USERPWD1VALUE] !== $updateParams[self::USERPWD2VALUE]) {
			Logger::collectAlert(Logger::ATTENTIONS, PWDNOTMATCH);
			return false;
		}

		$result = $this->users->updateUserPassword($verified['userid'], $updateParams[self::USERPWD1VALUE], true);

		if (!$result) {
			Logger::collectAlert(Logger::ATTENTIONS, PWDUPDERR);
			return false;
		}

		$r = $this->auth->clearActivations($verified['userid']);

		if (!$r) {
			Logger::collectAlert(Logger::ATTENTIONS, ACTUSERERR);
		}

		Logger::collectAlert(Logger::SUCCESS, PWDUPDATED);
		return true;
	}


	function registrationAction(): array {

		$pageTitle = array(

			'pageTitle' 	=> 'Форма регистрации',
			'successfull'	=> false,
		);

		if(!$this->authSts['reg_status']) {

			Logger::collectAlert(Logger::INFORMATION, REGDISABLED);
			return $pageTitle;
		}

		$registrationParams = array(

			self::USERNAMEVALUE => false,
			self::USERMAILVALUE => false,
			self::USERPWD1VALUE => false,
			self::USERPWD2VALUE => false,
			self::CSRFVALUE 	=> false,
		);

		$registrationParams = $this->getInputParams($registrationParams, '_POST');

		if(!$this->isNotEmpty($registrationParams)) {
			return $pageTitle;
		}

		// Временно установленно

		if (!Csrf::verifyToken(self::CSRFKEY, false, $registrationParams[self::CSRFVALUE])) {
			Logger::collectAlert(Logger::ATTENTIONS, CSRFUNSUCCESSFULL);
			return $pageTitle;
		}

		if ($registrationParams[self::USERPWD1VALUE] !== $registrationParams[self::USERPWD2VALUE]) {

			Logger::collectAlert(Logger::ATTENTIONS, PWDNOTMATCH);
			return $pageTitle;
		}

		$userExist = $this->users->userExist($registrationParams[self::USERMAILVALUE]);

		if($userExist) {
			Logger::collectAlert(Logger::ATTENTIONS, USEREXIST);
			return $pageTitle;
		}

		$insert = $this->users->insertNewUser($registrationParams[self::USERMAILVALUE], $registrationParams[self::USERPWD1VALUE], $registrationParams[self::USERNAMEVALUE]);

		if (!$insert) {
			Logger::collectAlert(Logger::ATTENTIONS, ADDUSERERR);
			return $pageTitle;
		}

		$meta = $this->auth->generateActivations($registrationParams[self::USERMAILVALUE]);

		if(!$meta) {
			Logger::collectAlert(Logger::ATTENTIONS, ERRGENLINK);
			return $pageTitle;
		}

		// TODO: Отправка емайла пользователю для восстановления пароля
		// TODO: сделать генерацию ссылок

		$source = Router::getRoute('/verifreg');

		$link = HOST .$source['url'].'/?'.self::USERIDVALUE.'=' . $meta['id'] . '&'.self::CONFRHSHVALUE.'=' . $meta['cofirm'] . '&'.self::TOKENHSHVALUE.'=' . $meta['token'];

		Logger::collectAlert(Logger::INFORMATION, $link);

		$pageTitle['successfull'] = true;

		return $pageTitle;
	}


	function verifyUserRegistration(): bool {

		if(!$this->authSts['reg_status']) {
			Logger::collectAlert(Logger::INFORMATION, REGDISABLED);
			return false;
		}

		$registrationConfirm = $this->verifyUserActivation();

		if(!$this->isNotEmpty($registrationConfirm)) {
			//Logger::collectAlert(Logger::ATTENTIONS, REGATRRERR);
			return false;
		}

		$status = $this->auth->activateRegisteredUser($registrationConfirm[self::USERIDVALUE]);

		if (!$status) {
			Logger::collectAlert(Logger::ATTENTIONS, USERACTERR);
			return false;
		}

		$this->auth->clearActivations($registrationConfirm[self::USERIDVALUE]);

		Logger::collectAlert(Logger::SUCCESS, USERACTIVED);
		return true;
	}


	// Функция которая возвращает данные из вне чтобы не дублировать код впихнул все сюда

	private function getInputParams(array $params, string $method, bool $silence=false): ?array {

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
					Logger::collectAlert(Logger::ATTENTIONS, EMPTYFIELDSEXIST);
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
				case self::USERMAILVALUE:
					$max = $this->authParams['transport']['userEmailMaxSym'];
					$min = $this->authParams['transport']['userEmailMinSym'];
				break;
				case self::USERPWD1VALUE:
				case self::USERPWD2VALUE:
					$max = $this->authParams['transport']['userPasswordMaxSym'];
					$min = $this->authParams['transport']['userPasswordMinSym'];
				break;
				case self::TOKENHSHVALUE:
				case self::CONFRHSHVALUE:
					$max = $this->authParams['transport']['userTokenHashMaxSym'];
					$min = $this->authParams['transport']['userTokenHashMinSym'];
				break;
				case self::USERNAMEVALUE:
					$max = $this->authParams['transport']['userNameMaxSym'];
					$min = $this->authParams['transport']['userNameMinSym'];
				break;
				case self::USERIDVALUE:
					$max = $this->authParams['transport']['useridMaxSym'];
					$min = $this->authParams['transport']['useridMinSym'];
				break;
				case self::CSRFVALUE:
					$max = $this->authParams['transport']['csrfTokenMaxSym'];
					$min = $this->authParams['transport']['csrfTokenMinSym'];
				break;
				default:
					$max = 0;
					$min = 0;
				break;
			}

			if ($this->isMoreThan($value, $max)) {
				if(!$silence)
					Logger::collectAlert(Logger::ATTENTIONS, sprintf(ERRMAXSYMLIMIT, $max));
				return false;
			}

			if($this->isLessThen($value, $min)) {
				if(!$silence)
					Logger::collectAlert(Logger::ATTENTIONS, sprintf(ERRMINSYMLIMIT, $min));
				return false;
			}

			if($key == self::USERMAILVALUE && !$this->mainValidator($value, 'email')) {
				if(!$silence)
					Logger::collectAlert(Logger::ATTENTIONS, ERRMAIL);
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
