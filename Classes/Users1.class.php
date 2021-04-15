<?php 

/**
*  	Класс работы с пользователями веб сайта
*/
class Users extends Database {
	
	function __construct() { 

		parent::__construct(true);

		$this->mod = new Modifier();
	}

	// Выводи список пользователей указанных по определенным параметрам

	protected function listUsers(array $params=array('begin'=>true,'finish'=>true,'middle'=>true)): ?array {

		// def: index to filter 
		// [A-Z], [activated/not],[deleted]


	}

	// Получаем профиль пользователя для контроллеров (!) не дальше!

	protected function getUser($uid): ?array {

		if (empty($uid) || !$this->userExist($uid, true)) { return null; }

		if  (!empty($this->profile)) { 

			$profile = $this->profile;
			
			unset($profile['password']);

			return $profile; 
		}
		
		// Возвращаем профиль пользователя
	}

	// Редактирование пользователя

	protected function editUser(int $userid, array $params): bool {

		/*
			name, email, password, picture, token, activation, regdate, lastvisit,
		*/

	}

	// Удаляет указанного пользователя
	// NB! Не использовать (!) 

	protected function removeUser(int $userid): bool {

		$userid = intval($userid);

		if(!$this->userExist($userid)) { 

			throw new Exception('Пользователь, '.$userid.', не найден!', 1);
			return false;
		} 

		$sql = 'DELETE FROM users WHERE user_id = :userid';

		$binder = array(':useremail' => $userid);

		$this->preAction($sql, $binder);

		if(!$this->doAction()) {

			throw new Exception('Ошибка SQL запроса!', 1);
			return false; 
		}

		return true;

	}

	private $mod; // new Modifier();

	// Добавляем нового пользователя

	protected function insertUser(array $params): bool {

		$defParams = array('useremail', 'username', 'userpass');

		foreach ($defParams as $key => $value) {
			
			if (empty($value)) { 

				throw new Exception('Не указаны нужные параметры!', 1);
				return false;
			}
		}

		/*
		if(!isset($params['useremail']) || $this->userExist($params['useremail'], true)) { 

			throw new Exception('Пользователь зарегестрирован в системе!', 1);
			return false; 
		}
		*/



		$uniqHash = $this->mod->strToHash($params['userpass']);
		$time = time();



	}

	private $profile = array(); // Устанавливаем профиль пользователя с которым работаем

	// Проверка существует пользователь или нет

	public function userExist($uid, bool $setprofile=false): bool {

		if(empty($uid)) { 

			throw new Exception('Идентификатор не указан!', 1);
			return false; 
		}

		if ($setprofile) {
			$sql = 'SELECT 
			            `user_id`      as uniqid,
			            `user_name`    as name,
			            `user_email`   as email,
			            `user_password`as password, 
			            `user_registration_date` as regdate,
			            `user_last_visit` as lastvisit,
			            `user_activated` as actstatus,
			            `user_picture` as userpicture
			            FROM `users`';
		} else {
			$sql = 'SELECT COUNT(*) as uniqid FROM users';
		}

		if(is_int($uid) || is_numeric($uid)) {

			$sql  .= ' WHERE user_id = :userid LIMIT 1';
            $binder = array(':userid' => intval($uid));
        } else {
        	
        	$sql  .= ' WHERE user_email = :useremail LIMIT 1';
        	$binder = array(':useremail' => $uid);	
        }

        $this->preAction($sql, $binder);

        if(!$this->doAction()) { 

        	throw new Exception('Не могу выполнить SQL запрос!', 1);
        	return false; 
        }

        $r = $this->postAction()->fetch();
		
        if(!empty($r) && isset($r['uniqid'])) {

        	if ($setprofile) {
        	
        		$this->profile = $r;
        	}

        	return true;
        }

        throw new Exception('Пользователь не найден!', 1);
        return false;
	}
}