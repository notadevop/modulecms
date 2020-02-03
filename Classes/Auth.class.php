<?php 


//class Auth extends Database  {
class Auth extends Database {
	
	function __construct() {

		parent::__construct(true);

		$userProfile = array();
	
		$this->modifier = new Modifier();
	}

	private $modifier;

	function __destruct() { $this->resetAction(); }

	// -----------------------------------------------

	private function getUserProfile($idoremail): ?array {


		
		if (is_int($idoremail) || is_numeric($idoremail)) {

			$sql = 'SELECT 
			`user_id` as id, 
			`user_name` as name, 
			`user_email` as email, 
			`user_password` as password,  
			`user_registration_date` as regdate,
			`user_last_visit` as lastvisit,
			`user_activated` as actstatus
			FROM `users` WHERE user_id = :userid LIMIT 1';

			$binder = array(':userid' => intval($idoremail));
		} else {

			$sql = 'SELECT 
			`user_id` as id, 
			`user_name` as name, 
			`user_email` as email, 
			`user_password` as password,  
			`user_registration_date` as regdate, 
			`user_last_visit` as lastvisit,
			`user_activated` as actstatus
			FROM `users` WHERE user_email = :useremail LIMIT 1';

			$binder = array(':useremail' => $idoremail);
		}

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return null; }

		$profile = $this
				->postAction()
				->fetch();

		return !empty($profile) ? $profile : null;
	}

	// -----------------------------------------------

	public function userNotBlocked(string $useremail): bool {

		$profile = $this->getUserProfile($useremail);

		// TODO: установить в базу таблицу с settings и там указывать все настройки 
		// Например указать привелегию для блокировки пользователя 
		// и подставить тут выбраз из настроек 

		// ==> SQL Table -> settings: DeletedUser: perm_id <-- FK 

		return $profile['actstatus'] == 0 ? false : true;
	}

	// ----------------------------------------------

	public function userIsActivated(string $useremail): bool {

		$profile = $this->getUserProfile($useremail);

		if (empty($profile['regdate'])) { return false; }

		if ($profile['actstatus'] == 0) { return false; }

		return true;
	}

	// ---------------------------------------------

	public function userExist($emailorid): bool {

		$id = $this->getUserProfile($emailorid)['id'];

		return (empty($id)) ? false : true;
	}

	//  -------------------------------------------- 

	// TODO: Переделать метод для поддержки нескольких входов и сессий, для входа с нескольких браузеров!!!!!

	private function updateUserHash(string $userid, bool $newLogin=false): ?string {

		$userid = intval($userid);

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

			debugger('ПОЧИНИТЬ ВРЕМЯ ПРОВЕРКИ ХЕША БАЗЕ ДАННЫХ: '.$interval->format('%s'), __METHOD__);

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

		if(!$this->doAction()) { return null; }

		return $updateHash;
	}


	// ---------------------------------------------

	public function findUser(string $useremail, string $userpass): ?array {

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

	// ----------------------------------------------

	public function insertNewUser(string $email, string $pass, string $username): bool{

		$profile = $this->getUserProfile($email);

		if (!empty($profile)) { return false; }

		$sql = 'INSERT INTO users () VALUES ()';

		$binder = array(
					':usermail' => $email,
					':password'	=> $this
										->modifier
										->strToHash($userpass),
					':username'	=> $username
		);

		// TOOD: Добавить так же роли в базу данных данных по привелегиям

		return false;		
	}

	// ---------------------------------------------

	public function authUser(string $email, string $userhash): ?array {

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

	// -------------------------------------------

	public function verifyActivations(int $userid, string $token, string $confirm, bool $verifytime=false): bool {

		$userid = intval($userid);

		$sql = 'SELECT 
					activation_token as token, 
					activation_confirm as confirm, 
					activation_created as created 
				FROM users_activation 
				WHERE activation_user_id = :actuid LIMIT 1';

		$binder = array(':actuid' => $userid);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return false; }

		$activator = $this
			->postAction()
			->fetch();

		if ($token !== $activator['token'] || $confirm !== $activator['confirm']) { return false; }

		if (!$verifytime) { return true; }

		$past 	= strtotime($activator['created']);
		$now 	= strtotime(time());

		$past 	= new DateTime($past);
		$now 	= new DateTime($now);

		$interval = $past->diff($now);

		debugger('ПОЧИНИТЬ ВРЕМЯ ДЛЯ ПРОВЕРКИ АКТИВАЦИИ: '.$interval->format('%h'), __METHOD__);

		if ($interval->format('%h') > REGWAITER) { 
		
			$this->clearActivations($userid); 
			return false; 
		}
	
		return true;
	}

	// --------------------------------------------

	public function updateActivations(int $userid): ?array {

		$userid = intval($userid);

		$sql = 'SELECT COUNT(*) as count 
				FROM users_activation WHERE activation_user_id = :actuid LIMIT 1';

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

	// --------------------------------------------

	public function clearActivations(int $userid): bool {

		$userid = intval($userid);

		$profile = $this->getUserProfile($userid);

		if (empty($profile)) { return false; }

		$sql = 'DELETE FROM users_activation WHERE activation_user_id = :uid';

		$binder = array(':uid' => $userid);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return false; }

		return true;
	}

	// --------------------------------------------

	public function updateLoginPassword(int $userid, string $userpass): bool {

		$userid = intval($userid);

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

	// --------------------------------------------

	// Генерирует токен и подтверждение для восстановления или подтверждения регистрации 

	public function generateActivations(string $usermail): ?array{

		$id = $this->getUserProfile($usermail)['id'];

		if (empty($id)) { return null; }

		// Генерируем хеш код для восстановления

		$t = $this->updateActivations($id);

		if (!empty($t)) { 

			$t['id'] = $id;
			return $t; 
		}

		return null;
	}

	// --------------------------------------------

	public function activateOrBlockUser(int $userid, bool $block=false): bool {

		$userid = intval($userid);

		$profile = $this->getUserProfile($useremail);

		if (empty($profile['id'])) { return false; }

		// проверить время регистрации пользователя, если отсутствует то обновить

		$sql = 'UPDATE users SET users_activated = :status WHERE user_id = :uid';

		$binder = array(
			':uid' 		=> $userid,
			':status'	=> (!$block ? 1 : 0)
		);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return false; }

		return true;
	}

	// --------------------------------------------

	public function deleteNotActivatedUsers() {


	}


	// --------------------------------------------

	// -> terminate all connections 
	function logout(string $userid, bool $termalldev = false): bool {

		// Удаление cесии данного пользователя или всех сессий из базы данных

		return false;
	}
	
	//function registration() {}
	//function confirmRegistration() {}
	//function confirmRestoration() {}
	//function updateUserProfile() {}
	
}