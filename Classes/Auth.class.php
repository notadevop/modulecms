<?php 


class Auth extends Database {
	
	private $userprofile;
	private $modifier;
	private $dateClass;
	private $visitor;

	private $users;

	function __construct(){

		parent::__construct(true);
		$this->modifier = new Modifier();
		$this->visitor 	= new Visitor();


		$this->users = new Users();

		// Временно!!!!! проверяем по времени разницу будет заменен на класс

		$this->dateClass = function($time1, $time2, $timetype='%d', $interval): bool {

			$current 	= strtotime($time1);
			$tomorrow 	= strtotime($time2);

			$current 	= new DateTime($current);
			$tomorrow 	= new DateTime($tomorrow);

			$interval 	= $current->diff($tomorrow);

			debugger('ПОЧИНИТЬ ВРЕМЯ ПРОВЕРКИ ХЕША БАЗЕ ДАННЫХ: '.$interval->format('%s'), 'Замыкание в dateClasse');

			return $interval->format($timetype) > $interval ? true : false;
		}; 
	}

	// Проверяем активирован пользователь или нет

	public function userActivated(string $useremail): bool{

		$profile = $this
					->users
					->getUserProfile($useremail);

		if (empty($profile['lastvisit']) || $profile['lastvisit'] == 0 || $profile['actstatus'] == 0) 
			{return false;}

		// Проверка привелегий пользователя, если их нет то пользователь не активирован

		$sql = 'SELECT DISTINCT COUNT(*) as counter FROM user_role WHERE user_id IN (SELECT user_id FROM users WHERE user_email = :useremail)';

		$binder = array(':useremail' => $useremail);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return false; }

		$prev = $this
					->postAction()
					->fetch()['counter'];

		return $prev == 0 ? false : true;
	}

	// Проверяем существует пользователь или нет 

	public function userExist($id): bool{

		return !empty($this
					->users
					->getUserProfile($id)['id']) ? true : false; 
	}

	// Устанавливаем или обновляем хеш пользователя и возвр. для сохранения

	private function updateUserhash(int $userid, bool $newLogin=false): ?string{

		$sql = 'SELECT `token_hash` as token, `token_created` as tcreated, 
				`token_expires` as texpires FROM `user_tokens` 
				WHERE `token_user_id` = :uid LIMIT 1';

		$binder = array(':uid' => $userid);

		$this->preAction($sql, $binder);

		if (!$this->doAction()) {return null;}

		$result = $this
					->postAction()
					->fetch();

		$rand 	= rand(30, 100);
		$newHash= $this
					->modifier
					->randomHash($rand, false);

		$updateHash = '';

		if (empty($result['token']) || $newLogin) {

			$updateHash = $newHash;

			$sql = 'INSERT INTO user_tokens 
					(token_user_id, token_user_agent, token_hash, token_created, token_expires) 
					VALUES (:userid, :uagent, :thash, :tcreated, :texpires)';
		} else {

			$sql = 'UPDATE user_tokens 
			SET token_user_agent = :uagent,
			token_hash = :thash,
			token_created = :tcreated,
			token_expires = :texpires 
			WHERE token_user_id = :userid';

			// Тут обновляем хеш когда время истекло

			$dateClass = $this->dateClass;

			if ($dateClass($result['tcreated'],$result['texpires'],'%d', UPDATEAUTHINTERVAL)) {

				# $updateHash = $newHash;

				debugger('Время истекло нужно обновлять хеш!');
			} 

			# $updateHash = $result['token'];
			$updateHash = $newHash;
		}	

		$binder = array(
				':userid' 			=> $userid,
				':uagent' 			=> serialize($this->visitor->get_data()),
				':thash'			=> $updateHash,
				':tcreated'			=> time(),
				':texpires' 		=> strtotime('+'.UPDATEAUTHINTERVAL.' Days') // time() +3600*24
			);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return null; }

		$sql = 'UPDATE users SET user_last_visit = :lastime WHERE user_id = :uid';

		$binder = array(
				':uid' => $userid, 
				':lastime' => time()
		);

		$this->preAction($sql, $binder);
		$this->doAction();

		return $updateHash;
	}

	// Проверяем авторизация по паролю и емайлу, возвращаем массив с данными

	public function findUser(string $useremail, string $userpass): ?array{

		$profile = $this
						->users
						->getUserProfile($useremail);

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

	// Проверяем аутентификацию пользователя по емайлу и токену из куки

	public function authUser(string $useremail, string $userhash): ?array{

		$sql = 'SELECT 
				t1.user_id as id, 
				t1.user_name as name,  
				t1.user_registration_date as regdate,
				t2.token_user_agent as uagent, 
				t2.token_hash as thash, 
				t2.token_created as tcreated, 
				t2.token_expires as texpires 
			FROM users as t1 JOIN user_tokens as t2 ON t1.user_id = t2.token_user_id
			WHERE t1.user_email = :useremail';

		$binder = array(':useremail' => $useremail);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) { return null; }

		$profile = $this
						->postAction()
						->fetchAll();	

		if (empty($profile[0])) { return null; }

		$profile = $profile[0];

		$dbfinger 	= $this
						->modifier
						->createFingerprint($profile['thash'], $profile['uagent']);
	
		$uagent 	= serialize($this->visitor->get_data());

		$userfinger = $this
						->modifier
						->createFingerprint($userhash, $uagent);

		if ($dbfinger !== $userfinger) { return null; }

		return array( 
			'userid' 	=> $profile['id'],
			'username' 	=> $profile['name'],
			'useremail' => $useremail,
			'userregd' 	=> $profile['regdate'],
			'tokenHash'	=> $this->updateUserHash($profile['id'])
		);
	}


	// Активируем нового пользователя для возможности авторизации

	public function activateRegisteredUser(string $userid): bool{

		$profile = $this
						->users
						->getUserProfile($userid);

		if (empty($profile['id'])) { return false; }

		// Тут устанавливаем привелегии только зарегестрированного пользователя 
		// в настройках системы можно установить какие привелегии пользователь получает

		$defPerms = 4; // <== Вытаскиваем из настроек указанные для регистрации привелегии

		$sql = 'INSERT INTO user_role (user_id, role_id) VALUES (:userid, :roleid)';

		$binder = array(
			':userid' => $userid,
			':roleid' => $defPerms
		);

		$this->preAction($sql, $binder);

		return !$this->doAction() ? false : true;
	}



	// Активирует или деактивирует указанного пользователя

	public function activateOrBlockUser(int $userid, bool $block=false): bool{

		$profile = $this
						->users
						->getUserProfile($userid);

		if (empty($profile['id'])) { return false; }

		// проверить время регистрации пользователя, если отсутствует то обновить

		$sql = 'UPDATE users SET users_activated = :status WHERE user_id = :uid';

		$binder = array(
			':uid' 		=> $userid,
			':status'	=> (!$block ? 1 : 0)
		);

		$this->preAction($sql, $binder);

		return !$this->doAction() ? false : true;

	}

	// Обновляем пароль пользователя по указанному id 
	/*
	public function updateUserPassword(int $userid, string $userpass): bool{

		$profile = $this
						->users
						->getUserProfile($userid);

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

		return !$this->doAction() ? false : $this->clearActivations($userid);
	}
	*/

	// Удаляем всех пользователей которые не активировали свои аккаунты в течении указанного времени 
	// TODO: нужно для CRON 

	public function deleteNotActivatedUsers(): bool{

		// TODO: использовать INNER JOIN для DELETE и условия в SQL для чистого удаления

		//$sql = 'DELETE FROM users_activation WHERE ';

		$sql = 'SELECT user_id, user_last_visit, user_activated FROM users';

		// Определяем время и разницу во времени 
		// и по ним определить кого удалить 
	}

	// Удаляем указанные сесси пользователя или все сессии НЕЯСНО.

	public function clearUserLogins(int $byspecuser=0): bool{

		// Удаление cесии данного пользователя или всех сессий 
		// из базы данных по token_expires и token_created

		return false;
	}

	// Проверяем активацию пользователя и для восстановления, и для регистрации 

	public function verifyActivations(int $userid, string $token, string $confirm, bool $vertime=false): bool{

		$profile = $this
						->users
						->getUserProfile($userid);

		if (empty($profile)) { return false; }

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

		if(!$activator) {return false;}

		if ($token !== $activator['token'] || $confirm !== $activator['confirm']) { return false; }

		$sql = 'UPDATE users SET user_activated = :act, user_last_visit = :lastv WHERE user_id = :uid';

		$binder = array(
			':act' 	=> 1,
			':uid'	=>$userid,
			':lastv'=>time()
		);		

		$this->preAction($sql, $binder);

		return !$this->doAction() ? false : true;

		// Этот код нужно использовать в крон действии!!! каждые 24-48 часов (указать так, чтобы спамеры не загружали и пользователь мог повторить активацию)

		//$this->clearActivations($userid, $vertime); 

		/*
		$dateClass = $this->dateClass;

		if ($dateClass($activator['created'],time(),'%h', UPDATEAUTHINTERVAL)) {

			$this->clearActivations($userid); 
			return false; 
		} 
		*/
	}

	// устанавливаем активацию для подтверждения 

	public function updateActivations(int $userid): ?array{

		$this->clearActivations($userid, true); // Тут удаляем все просроченные активации, 

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
				activation_created = :actdate, 
				activation_expired = :actexpire
				WHERE activation_user_id = :actuid';

		} else {
			$sql = 'INSERT INTO users_activation 
				(activation_user_id, activation_token, activation_confirm, 
				activation_created, activation_expired) 
				VALUES 
				(:actuid, :actoken, :actconfirm, :actdate, :actexpire)';
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
				':actexpire'	=> strtotime('+'.UPDATEAUTHINTERVAL.' Days')
		);

		$this->preAction($sql, $binder);
		if(!$this->doAction()) { return null; }

		return array(
				'cofirm' 	=> $binder[':actconfirm'], 
				'token' 	=> $binder[':actoken']
		);
	}

	// Удаляем активацию из базы данных

	public function clearActivations(int $userid, bool $cleanbytime=false): bool{

		$profile = $this
						->users
						->getUserProfile($userid);

		if (empty($profile)) { return false; }

		if($cleanbytime) {

			$sql = 'DELETE FROM users_activation 
					WHERE activation_user_id = :uid AND activation_expired < :curtime';

			$binder = array(':uid' => $userid, ':curtime' => time());
		} else {

			$sql = 'DELETE FROM users_activation WHERE activation_user_id = :uid';
			$binder = array(':uid' => $userid);
		}

		$this->preAction($sql, $binder);

		return !$this->doAction() ? false : true;
	}

	// Генерируем активацию 

	public function generateActivations(string $useremail): ?array{

		$profile = $this
						->users
						->getUserProfile($useremail);

		if (empty($profile['id'])) { return null; }

		$t = $this->updateActivations($profile['id']);

		if (!empty($t)) { 

			$t['id'] = $profile['id'];
			return $t; 
		}

		return null;
	}
}