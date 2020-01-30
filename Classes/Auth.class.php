<?php 


//class Auth extends Database  {
class Auth extends Database {
	
	function __construct() {

		parent::__construct(true);

		$userProfile = array();
	
		$this->modifier = new Modifier();
	}

	private $modifier;

	function __destruct() {

		$this->resetAction();
	}

	// Функция которая возвращает профиль пользователя 

	private function getUserProfile($identify): ?array {

		$sql = 'SELECT 
			`user_id` as id, 
			`user_name` as name, 
			`user_email` as email, 
			`user_password` as password,  
			`user_registration_date` as regdate 
			FROM `users` WHERE user_email = :useremail LIMIT 1';

		$binder = array(':useremail' => $identify);

		
		if (is_int($identify) || is_numeric($identify)) {

			$sql = 'SELECT 
			`user_id` as id, 
			`user_name` as name, 
			`user_email` as email, 
			`user_password` as password,  
			`user_registration_date` as regdate 
			FROM `users` WHERE user_id = :userid LIMIT 1';

			$binder = array(':userid' => $identify);
		} 

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return null; }

		$profile = $this
				->postAction()
				->fetch();

		return !empty($profile) ? $profile : null;
	}


	public function userNotBlocked(string $useremail): ?bool {

		$userid = $this->getUserProfile($useremail)['id'];

		if (empty($userid)) { return null; }

		$priv = new Priveleges();
		$priv->initRoles($userid);

		if ($priv->hasPrivilege('deleted')) { return null; }

		if ($priv->hasPrivilege('blocked')) { return false; }
			
		return true;
	}

	function userIsActivated(string $useremail): bool {

		$profile = $this->getUserProfile($useremail);

		return !empty($profile['regdate']) ? true : false;
	}

	// Проверяет сущуествует пользователь или нет, 
	// если включить checkBan то проверяет забанен ли пользователь или нет

	public function userExist(string $useremail): bool {

		return (empty($this->getUserProfile($useremail)['id'])) ? false : true;
	}


	//  ------------- UPDATE HASH ----------------- 

	// TODO: Переделать метод для поддержки нескольких входов и сессий, для входа с нескольких браузеров!!!!!

	private function updateUserHash(string $userid, bool $newLogin=false): ?string {

		$visitor = new Visitor();

		$sql = 'SELECT 
				token_hash as token,
				token_created as tcreated,
				token_expires as texpires 
				FROM user_tokens 
				WHERE token_user_id = :uid LIMIT 1';

		$binder = array(':uid' => $userid );

		$this->preAction($sql, $binder);
		$this->doAction();

		$result = $this
					->postAction()
					->fetch();

		$rand 		= rand(30, 100);
		$newHash 	= $this
						->modifier
						->randomHash($rand, false);

		$updateHash = '';

		if (empty($result['token']) || $newLogin) {
		
			$sql = 'INSERT INTO user_tokens 
			(token_user_id, token_user_agent, token_hash, token_created, token_expires) 
			VALUES (:userid, :uagent, :thash, :tcreated, :texpires)';

			$updateHash = $newHash;

		} else {
	
			$sql = 'UPDATE user_tokens SET 
				token_user_agent = :uagent,
				token_hash  	= :thash,
				token_created 	= :tcreated,
				token_expires 	= :texpires 
				WHERE token_user_id = :userid';

			// TODO: Затычка -> нихрена не работает, че за фигня ????

			$current 	= strtotime($result['tcreated']);
			$tomorrow 	= strtotime($result['texpires']);

			$current 	= new DateTime($current);
			$tomorrow 	= new DateTime($tomorrow);

			$interval 	= $current->diff($tomorrow);

			debugger('Разница в днях: '.$interval->format('%s'), __METHOD__);

			//$updateHash = $interval->format('%d') > UPDATEAUTHINTERVAL ? $newHash : $result['token'];

			$updateHash = $newHash;
		}

		$binder = array(
				':userid' 			=> $userid,
				':uagent' 			=> serialize($visitor->get_data()),
				':thash'			=> $updateHash,
				':tcreated'			=> time(),
				':texpires' 		=> strtotime('+'.UPDATEAUTHINTERVAL.' Days')
			);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) {

			debugger('Хеш не обновлен и не добавлен в базу!',__METHOD__);
			return null;
		}

		return $updateHash;
	}


	// -----------------  LOGIN ------------

	function login(string $useremail, string $userpass): ?array {

		$profile = $this->getUserProfile($useremail);

		if (empty($profile)) { return null; }

		$dbpwdhash = $profile['password'];

		$userpwdhash = $this
						->modifier
						->strToHash($userpass);

		if( $dbpwdhash !== $userpwdhash ) { return null; }

		return array( 
			'userid' 	=> $profile['id'],
			'username' 	=> $profile['name'],
			'useremail' => $profile['email'],
			'userregd' 	=> $profile['regdate'],
			'tokenHash'=> $this->updateUserHash($profile['id'], false)
		);
	}


	// TODO: Для сохранения в куки шифруем токен и емайл, 
	// чтобы не могли получить оригиналы 

	function authin(string $email, string $userhash): ?array {

		$visitor= new Visitor();

		$sql = 'SELECT 
		t1.user_id as id, 
		t1.user_name as name,  
		t1.user_registration_date as regdate,
		t2.token_user_agent as uagent, 
		t2.token_hash as thash, 
		t2.token_created as tcreated, 
		t2.token_expires as texpires 
		FROM users as t1 JOIN user_tokens as t2 
		ON t1.user_id = t2.token_user_id
		WHERE t1.user_email = :useremail';

		$binder = array(':useremail' => $email);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return null; }

		$profile = $this
			->postAction()
			->fetchAll();

		if (empty($profile)) { return null; }

		$profile = $profile[0];

		// Генерируем fingerprint из базы 

		$dbfinger 	= $this
						->modifier
						->createFingerprint($profile['thash'], $profile['uagent']);

		// Генерируем fingerprint от пользователя из куки		
		$uagent 	= serialize($visitor->get_data());

		$userfinger = $this
						->modifier
						->createFingerprint($userhash, $uagent);

		// Если отпечатки не похожи выходим из аутентификаци 
		if ($dbfinger !== $userfinger) { return null; }

		return array( 
			'userid' 	=> $profile['id'],
			'username' 	=> $profile['name'],
			'useremail' => $email,
			'userregd' 	=> $profile['regdate'],
			'tokenHash'	=> $this->updateUserHash($profile['id'])
		);
	}

	function verifyActivations(int $userid, string $token, string $confirm, bool $verifexpir=false): bool {

		$sql = 'SELECT 
					activation_token as token, 
					activation_confirm as confirm, 
					activation_created as created 
				FROM users_activation 
				WHERE activation_user_id = :actuid LIMIT 1';

		$binder = array(':actuid' => $userid);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { 
			debugger('SQL не прошел!',__METHOD__);
			return false; }

		$activator = $this
			->postAction()
			->fetch();

		if ($token !== $activator['token'] || $confirm !== $activator['confirm']) { 

			debugger('Неcовпадают ключи: ',__METHOD__);
			return false; }

		if ($verifexpir) {

			$past 	= strtotime($activator['created']);
			$now 	= strtotime(time());

			$past 	= new DateTime($past);
			$now 	= new DateTime($now);

			$interval 	= $past->diff($now);

			debugger('Разница в часах: '.$interval->format('%h'), __METHOD__);

			if ($interval->format('%h') > REGWAITER) { return false; }
		}

		// TODO: Удалить этот ключ из базы данных в целях безопасности

		return true;
	}

	function updateActivations(int $userid): ?array {

		$sql = 'SELECT COUNT(*) as count 
				FROM users_activation 
				WHERE activation_user_id = :actuid LIMIT 1';

		$binder = array(':actuid' => $userid);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return null; }

		$counter = $this
			->postAction()
			->fetch()['count'];

		if ($counter > 0) { 

			$sql = 'UPDATE users_activation SET 
				activation_token = :actoken,
				activation_confirm = :actconfirm,
				activation_created = :actdate
				WHERE activation_user_id = :actuid';

		} else {
			$sql = 'INSERT INTO users_activation 
				(activation_user_id, activation_token, activation_confirm, activation_created) 
				VALUES (:actuid, :actoken, :actconfirm, :actdate)';
		}

		$binder = array(
				':actuid'		=> $userid,
				':actoken'		=> $this
						->modifier
						->randomHash(rand(30, 50), false),
				':actconfirm'	=> $this
						->modifier
						->randomHash(rand(30, 50), false),
				':actdate'		=> time(),
		);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return null; }

		return array('cofirm' => $binder[':actconfirm'], 'token' => $binder[':actoken']);
	}

	function clearActivations(int $userid): bool {

		$profile = $this->getUserProfile($userid);

		if (empty($profile)) { return false; }

		$sql = 'DELETE FROM users_activation WHERE activation_user_id = :uid';

		$binder = array(':uid' => $userid);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return false; }

		return true;
	}

	function updateLoginPassword(int $userid, string $userpass): bool {

		$profile = $this->getUserProfile($userid);

		if (empty($profile)) { return false; }
		
		$userpass = $this
						->modifier
						->strToHash($userpass);

		$sql = 'UPDATE users SET user_password = :userpass WHERE user_id = :uid';

		$binder = array(
			':uid'		=> $userid,
			':userpass' => $userpass
		);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return false; }

		return $this->clearActivations($userid); 
	}

	// Генерирует токен и подтверждение для восстановления или подтверждения регистрации 

	function generateActivations(string $usermail): ?array{

		$profile = $this->getUserProfile($usermail);

		if (empty($profile)) { return null; }

		// Генерируем хеш код для восстановления

		$t = $this->updateActivations($profile['id']);

		if (!empty($t)) { 

			$t['id'] = $profile['id'];
			return $t; 
		}

		return null;
	}

	// -> terminate all connections 
	function logout(string $userid, bool $termalldev = false): bool {

		// Удаление всех сессий из базы данных

		return false;
	}
	
	//function registration() {}
	//function confirmRegistration() {}
	//function confirmRestoration() {}
	//function updateUserProfile() {}
	
}