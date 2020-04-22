<?php 

// Контроллер для редактирования привелегий и ролей пользователя 

// а так же определения првелегий для определенных страниц

class PrivelegesController {

	private $priv;
    private $perms = array();

    function __construct() {
    
    	$userid = defined('PROFILE') ? PROFILE['userid'] : 0;

    	$this->priv = new Priveleges();
		$this
			->priv
			->initRoles($userid);

    }

    function getAllPerms() {

    	return $this
    				->priv
    				->getPerms();
    }

    function verifyRest(array $perms) {

    	foreach ($perms as $value) {
				
	    	if ($this
	    			->priv
	    			->hasPrivilege($value)) 
	    	{ return true; } 
		}
		
		return false;
    }



}