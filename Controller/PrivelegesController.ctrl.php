<?php 

// Контроллер для редактирования привелегий и ролей пользователя 

// а так же определения првелегий для определенных страниц

class PrivelegesController extends Errors{

	private $priv;
    private $perms = array();
    private $userid;

    function __construct() {
    
    	$this->userid = defined('PROFILE') ? PROFILE['userid'] : 0;
    	$this->priv = new Priveleges();
	}

	function initUser($userid): void {

		$this->userid = $userid;
	}
   
    function getAllPerms() {

    	// Инцилизирует пользователя 
    	$this
			->priv
			->initRoles($this->userid);

		// Возвращает все привелегии пользователя
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