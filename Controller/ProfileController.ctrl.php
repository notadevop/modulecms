<?php 


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

        // Проверяем  есть у даннго пользователя привелегии
        
        if (!$checkPerm) {
            Logger::collectAlert('attentions', NOPRIVELEGES);
            return null;
        } 

        $userid = intval($uid);

        // Фильтруем данные 

        if ($userid == 0) {
            Logger::collectAlert('attentions', USERNULL);
            return null;
        }

        $users = $this->users->getUserProfile($userid);

        if (!$users) {
            Logger::collectAlert('attentions', USERNOTFOUND);
            return null;
        }
        return $users;
	 }

     function editUserProfile($uid = 0) {

     }

     function getAllUsers(): ?array {

        $arrUsers = array(
            'viewListUsers' => false,
            'allowEditing'  => false,
            'allowRemoving' => false,
            'users'         => null
        );

        $perms = array(
            $this->granter->getAllPerms()[0]['perm_desc'],
            $this->granter->getAllPerms()[1]['perm_desc'],
        );

        $this->granter->initUser();
        $checkPerm  = $this->granter->verifyRest($perms);
        $users      = $this->users->getListUsers();

        if (!$users) {
            Logger::collectAlert('attentions', NOLISTUSERS);
        }

        $this->granter->initUser();
        $checkPerm = $this->granter->verifyRest($perms);

        // Проверяем  есть у даннго пользователя привелегии
        if (!$checkPerm) {
            Logger::collectAlert('attentions', NOPRIVELEGES);
            return null;
        } else {

            $arrUsers['allowEditing']   = true;
            $arrUsers['allowRemoving']  = true;
        }

        $arrUsers['users'] = $users;
        return $arrUsers;
     }

     // Фильтрует данные пользователя, что выводить

     private function filterPersonalInfo(array $userdata): ?array {

     }
}