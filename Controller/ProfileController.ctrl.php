<?php 


class ProfileController extends Errors{

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

    	$checkPerm = $this
    					->granter
    					->verifyRest($perms);

	    if (!$checkPerm) {
	    	
	    	$this->collectErrors('noaccess', '<b>Доступ Запрещен! Недостаточно привелегий.</b>');

	    	return '';
	    } 

	    return array('access' => PROFILE);
	 }
}