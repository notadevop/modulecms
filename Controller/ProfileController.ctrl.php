<?php 


class ProfileController {

    function __construct() {

    	$this->granter = new PrivelegesController();
    	$this->users = new Users();
    }

    private $granter;
    private $users;

    // Получаем доступ только к указанному пользователю
    // если пользователь не указан или такого нету 
    // Выводи сообщение, что нет такого пользователя 

    function getUserProfile($userid=0)  {

    	// Привелегии которые разрещенны пользователю для посещения этой страницы 
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

	    if (!$checkPerm) {

            Logger::collectAlert('warnings', '<b>Пользователь не указан.</b>');
	    	return '';
	    } 

	    return array('access' => PROFILE);
	 }
}