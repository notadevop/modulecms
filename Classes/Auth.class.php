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

	private function getUserProfile(string $useremail): ?array {

		$sql = 'SELECT 
		`user_id` as id, 
		`user_name` as name, 
		`user_email` as email, 
		`user_password` as password,  
		`user_registration_date` as regdate 
		FROM `users` 
		WHERE user_email = :useremail LIMIT 1';

		$binder = array(':useremail' => $useremail);

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

		$s = null;

		if ($priv->hasPrivilege('deleted')) {
			
			$s = null; 	

		} else if ( $priv->hasPrivilege('blocked') ) {
			
			$s = false; 
		} else { 
			
			$s = true;
		}

		return $s;
	}

	// Проверяет сущуествует пользователь или нет, 
	// если включить checkBan то проверяет забанен ли пользователь или нет

	public function userExist(string $useremail): bool {

		$userid = $this->getUserProfile($useremail)['id'];

		return (empty($userid) || $userid == null) ? false : true;
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

		if( $dbpwdhash !== $userpwdhash ) { 
			debugger('разные пароли!',__METHOD__);
			return null; }

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

		if (empty($profile)) { 
			debugger('Профиль не найден в базе!',__METHOD__);
			return null; }

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
		if ($dbfinger !== $userfinger) { 
			//debugger('из базы фингер: '.$dbfinger,__METHOD__);
			//debugger('пользователя фингер: '.$userfinger,__METHOD__);
			//debugger('Отпечатки не совпадают!',__METHOD__);
			return null; }

		return array( 
			'userid' 	=> $profile['id'],
			'username' 	=> $profile['name'],
			'useremail' => $email,
			'userregd' 	=> $profile['regdate'],
			'tokenHash'	=> $this->updateUserHash($profile['id'])
		);
	}

	function restoration(string $usermail): ?string{

		$profile = $this->getUserProfile($usermail);

		if (empty($profile)) { return null; }

		// Генерируем хеш код для восстановления

		$tokenHash = $this->updateUserHash($profile['id']);

		return $tokenHash;
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