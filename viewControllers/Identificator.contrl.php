<?php 

// Класс идентификации пользователя

class Identificator extends Filter {
	
	function __construct() {
		

		$this->authParams = array(

			'future' 	=> '+2 Hours',
			'past' 		=> '-2 Hours',
			'host' 		=> '/',
			'domain' 	=> HOST	

			// Параметры пользователя по умолчанию

			'userid'	=> 0,
			'username'	=> 'Анонимный пользователь',
			'priveleges'=> false,


			// Использовать из базы сохраненные параметры

			'loginEmailMaxSym'=> 32,
			'loginEmailMinSym'=> 7,
			
			'loginPasswordMax'=> 32,
			'loginPasswordMin'=> 6

			''
		);
	}

	private $authParams;
	private $users;

	function setUserProfile($profile=''): bool {

		$profileStatusInit = false;

		// Достаем дефольтные данные (статичные)
		$userProfile = array(

			'userid' 	=> $this->authParams['userid'],
			'username' 	=> $this->authParams['username'],
			'priveleges'=> $this->authParams['priveleges']
		);

		if($this->isNotEmpty($profile)) { 

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

	function loginAction(): bool {

		if(!defined('LOGINALLOW') || !LOGINALLOW) {

			Logger::collectAlert('warnings', 'Вход в систему отключен администратором!');
			return $this->setUserProfile(false);
		}

		$this->glob->setGlobParam('_POST');

		$loginParams = array(

			'loginmail',
			'loginpasswd'
		);

		foreach ($loginParams as $key => $value) {
			
			// Фильтруем основные веши!

			if(!$this->glob->isExist($key)) { 

				return $this->setUserProfile(false); 
			}

			$value = $this->glob->getGlobParam($key)

			if(!$this->isNotEmpty($value)) { 

				return $this->setUserProfile(false);  
			}

			$value = $this->mainSanitizer($value, 'encoding');
			$value = $this->mainSanitizer($value, 'magicquotes');
			$value = $this->mainSanitizer($value, 'fullspecchars');
			$value = $this->mainSanitizer($value, 'string');
			$value = $this->mainSanitizer($value, 'stripped');

			try {
				$this->ejectedWords($value);
			} catch (Exception $e) {
				Logger::collectAlert('warnings', $e->getMessage());
			}finaly{
				$loginParams[$key] = $value;
			}	
		}

		// фильтруем по уникальности 

		if($this->isMoreThan($loginParams['loginmail'],$authParams['loginEmailMaxSym']) || $this->isMoreThan($loginParams['loginpasswd'],$authParams['loginPasswordMaxSym'])) {

			Logger::collectAlert('warnings', 'Проверьте ваши поля в одном из поле преувеличенно число символов!');

			return $this->setUserProfile(false); 
		}

		if($this->isLessThen($loginParams['loginmail'],$authParams['loginEmailMinSym']) || $this->isLessThen($loginParams['loginpasswd'],$authParams['loginPasswordMin'])) {

			Logger::collectAlert('warnings', 'Проверьте ваши поля в одном из полей количество символов меньше разрешенного!');
			return $this->setUserProfile(false); 
		}

		if(!$this->mainValidator($loginParams['loginmail'], 'email')) {

			Logger::collectAlert('warnings', 'Указан некорректный емайл!');
			return $this->setUserProfile(false); 
		}

		$userExist = $this->users->userExist($loginParams['loginmail']);

		if(!$userExist) {

			Logger::collectAlert('warnings', 'Указанный пользователь не найден или удален!');
			return $this->setUserProfile(false); 
		}

		$userNotBlocked = $this->auth->userActivated($loginParams['loginmail']);

		if (!$userNotBlocked) {

			Logger::collectAlert('warnings', 'Указанный пользователь отправлен в бан!');
			return $this->setUserProfile(false);
		}

		$findUser = $this->auth->findUser($p['loginmail'], $p['loginpasswd']);

		if(empty($findUser)) {

			Logger::collectAlert('warnings', 'Неправильные имя или пароль!');
			return $this->setUserProfile(false);
		}

		$findUser['tokenHash'] = $this->auth->updateUserHash($profile['userid'], false);

		if(empty($findUser['tokenHash'])) {

			Logger::collectAlert('warnings', 'Ошибка генерации хеш кода!');
			return $this->setUserProfile(false);
		}






		// Перенаправление пользователя при определенном действии

	}
}