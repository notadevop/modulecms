<?php 

(defined('ROOTPATH') && defined('DS')) or die('something wrong!');

class Users extends Database {

      private $modifier;

   	function __construct(){ 

         parent::__construct(true);

         $this->modifier = new Modifier();
      }

   	// Получаем список всех пользователей зарегестрированных на сайте
   	function getListUsers(bool $all=false): ?array {

         /*
         $sql = 'SELECT 
         `user_id`, `user_name`, `user_email`, `user_password`, `user_last_visit`, `user_registration_date`, `user_activated`, `user_picture`  FROM `users` 
         WHERE `user_last_visit` != :lastv AND `user_activated` = :uact';

         $binder = array(
            ':lastv' => 0,
            ':uact'  => 1
         );

         $this->preAction($sql, $binder); 
         */

         $sql = 'SELECT 
         `user_id`, `user_name`, `user_email`, `user_password`, `user_last_visit`, `user_registration_date`, `user_activated`, `user_picture`  FROM `users` ';

         $this->preAction($sql); 


         if(!$this->doAction()) { return null; }

         $users = $this
                     ->postAction()
                     ->fetchAll();

         return !empty($users) ? $users : null;
      }

      function userExist($uid): bool {

         return !empty($this->getUserProfile($uid)['id']) ? true : false;
      }

   	// Получаем один профиль указанного пользователя 
   	function getUserProfile($uid): ?array {

         $sql = 'SELECT 
            `user_id`      as id,
            `user_name`    as name,
            `user_email`   as email,
            `user_password`as password, 
            `user_registration_date` as regdate,
            `user_last_visit` as lastvisit,
            `user_activated` as actstatus,
            `user_picture` as userpicture
            FROM `users`';

         if(is_int($uid) || is_numeric($uid)) {

            $sql  .= ' WHERE user_id = :userid LIMIT 1';
            $binder = array(':userid' => intval($uid));
         } else {

            $sql  .= ' WHERE user_email = :useremail LIMIT 1';
            $binder = array(':useremail' => $uid);
         }

         $this->preAction($sql, $binder);

         if(!$this->doAction()) {return null; }

         $profile = $this
                        ->postAction()
                        ->fetch();

         return !empty($profile) ? $profile : null;
      }

         // TODO: Добавить возможность сравнивать старый пароль и новый, для смены пароля в профиле

      function updateUserPassword(int $userid, string $userpass, bool $recovery=false,string $oldpass=''): bool {

         $profile = $this->getUserProfile($userid);

         if (empty($profile)) { return false; }

         $userpass = $this
                     ->modifier
                     ->strToHash($userpass);

         $sql = 'UPDATE users SET user_password = :userpass WHERE user_id = :uid';

         $binder = array(
            ':uid'      => $userid,
            ':userpass' => $userpass
         );

         $this->preAction($sql, $binder);

         return !$this->doAction() ? false : true;

      }

      function insertNewUser(string $useremail, string $userpass, string $username): bool {

         $profile = $this->getUserProfile($useremail);

         if (!empty($profile['id'])) { return false; }

         $sql = 'INSERT INTO 
         users (user_name, user_email, user_password, user_registration_date, user_last_visit, user_activated) 
         VALUES (:username, :usermail, :userpass, :userregdate, :userlastv, :useractiv)';

         $binder = array(
                  ':usermail'    => $useremail,
                  ':userpass'    => $this->modifier->strToHash($userpass),
                  ':username'    => $username,
                  ':userregdate'    => time(),
                  ':userlastv'   => 0,
                  ':useractiv'   => 0
         );

         $this->preAction($sql, $binder);

         return !$this->doAction() ? false : true;
      }

      // Обновляем существующую информацию пользователя 
      function updateUserProfile(int $userid, array $userparams): bool {return false;}
}