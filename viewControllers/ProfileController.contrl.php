<?php 


/**
 *      ПрофильКонтроллер выводит 
 *      * профили пользователя или пользователей
 *      * редактирования профилей пользователей
 *      * удаление/блокировка профилей пользователей
 *      * 
 * 
 */





class ProfileController {

    function __construct() {

    	$this->granter = new PrivelegesController();
    	$this->users   = new Users();
        $this->filter  = new Filter();
    }

    private $granter;
    private $users;
    private $filter;
    private $errors;

    function getUserProfile(int $uid = 0): ?array {

        // TODO: Сделать отключение показа социальных данных или профиля пользователям у которых нету привелегий смотреть профиль

        // Указываем в массиве кто имеет право смотреть профиль по определенным привелегиям
        $perms = array(
            $this->granter->getAllPerms()[0]['perm_desc'],
            $this->granter->getAllPerms()[1]['perm_desc'],
        );

        $this->granter->initUser();
        $checkPerm = $this->granter->verifyRest($perms);

        $userid = intval($uid);

        // Фильтруем данные 

        if ($userid == 0) {

            $userid = PROFILE['userid'];
            Logger::collectAlert(Logger::ATTENTIONS, USERNULL);
        }

        // Проверяем  есть у даннго пользователя привелегии
        
        if (!$checkPerm) {


            Logger::collectAlert(Logger::ATTENTIONS, NOPRIVELEGES);
            //return null;
        } 

        



        $user = $this->users->getUserProfile($userid);

        if (!$user) {
            Logger::collectAlert(Logger::ATTENTIONS, USERNOTFOUND);
            return null;
        }

        Logger::collectAlert(Logger::INFORMATION, USERNOTCOMPLETE);

        return $user;
	 }

     function editUserProfile($uid = 0) {

     }

     function getAllUsers(): ?array {

        $arrUsers = array(
            'viewListUsers' => false,
            'allowEditing'  => false,
            'allowRemoving' => false,
            'users'         => null,
            'pageTitle'     => 'Список пользователей',
        );

        $perms = array(
            $this->granter->getAllPerms()[0]['perm_desc'],
            $this->granter->getAllPerms()[1]['perm_desc'],
        );

        $this->granter->initUser();
        $checkPerm  = $this->granter->verifyRest($perms);
        $users      = $this->users->getListUsers();

        if (!$users) {
            Logger::collectAlert(Logger::ATTENTIONS, NOLISTUSERS);
        }

        $this->granter->initUser();
        $checkPerm = $this->granter->verifyRest($perms);

        // Проверяем  есть у даннго пользователя привелегии
        if (!$checkPerm) {
            Logger::collectAlert(Logger::ATTENTIONS, NOPRIVELEGES);
            return null;
        } else {

            $arrUsers['allowEditing']   = true;
            $arrUsers['allowRemoving']  = true;
        }

        $arrUsers['users']      = $users;

        return $arrUsers;
     }

     // Фильтрует данные пользователя, что выводить

     private function filterPersonalInfo(array $userdata): ?array {

     }
}