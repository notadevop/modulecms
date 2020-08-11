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

    private function filtration(string $input, array $options) {

        $this->filter->setVariables(

            array(
                'key' => array(
                    'value'     => $input,
                    'maximum'   => $options['maxSym'],
                    'minimum'   => $options['minSym']
                )
            )
        );

        $this->filter->cleanAttack('key', array(''));
    }


    function getUserProfile($uid = 0): ?array {

                // TODO: Проверяем привелегии пользователя который запрашивает профиль указанного юзера
        // Если привелегии не достаточно, выводим только базовую информацию
        // TODO: Сделать отключение показа социальных данных или профиля пользователям у которых нету привелегий смотреть профиль

        // Указываем в массиве кто имеет право смотреть профиль по определенным привелегиям
        $perms = array(

            $this
                ->granter
                ->getAllPerms()[0]['perm_desc'],
            $this
                ->granter
                ->getAllPerms()[1]['perm_desc'],
        );

        $this
            ->granter
            ->initUser();

        $checkPerm = $this
                        ->granter
                        ->verifyRest($perms);

        // Проверяем  есть у даннго пользователя привелегии
        if (!$checkPerm) {

            Logger::collectAlert('attentions', '<b>Недостаточно привелегий.</b>');
            return null;
        } 

        $userid = (int) $uid;


        // Фильтруем данные 

        if ($userid == 0) {

            Logger::collectAlert('attentions', '<b>Не могу вывести указанного пользователя</b>');
            return null;
        }

        $users = $this
                    ->users
                    ->getUserProfile($userid);

        if (!$users) {

            Logger::collectAlert('attentions', '<b>Указанный пользователь не найден!</b>');
            return null;
        }
        
        //debugger($users,__METHOD__);

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

            $this
                ->granter
                ->getAllPerms()[0]['perm_desc'],
            $this
                ->granter
                ->getAllPerms()[1]['perm_desc'],
        );

        $this
            ->granter
            ->initUser();

        $checkPerm = $this
                        ->granter
                        ->verifyRest($perms);

        $users = $this
                    ->users
                    ->getListUsers();

        if (!$users) {

            Logger::collectAlert('warnings', '<b>Не могу вывести список пользователей</b>');
        }

        $this
            ->granter
            ->initUser();

        $checkPerm = $this
                        ->granter
                        ->verifyRest($perms);

        // Проверяем  есть у даннго пользователя привелегии
        if (!$checkPerm) {

            Logger::collectAlert('attentions', '<b>Не хватает привелегий для показа этой страницы.</b>');
            return null;
        } else {

            $arrUsers['allowEditing'] = true;
            $arrUsers['allowRemoving'] = true;
        }

        $arrUsers['users'] = $users;

        return $arrUsers;
     }

     // Фильтрует данные пользователя, что выводить

     private function filterPersonalInfo(array $userdata): ?array {

     }
}