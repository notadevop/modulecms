<?php 

// Контроллер для редактирования привелегий и ролей пользователя 

// а так же определения првелегий для определенных страниц

class PrivelegesController extends Errors{

	private $priv;
    private $perms = array();
    private $userid;

    function __construct() {
    
    	$this->priv = new Priveleges();
	}

	function initUser($userid=0): void {

        $this->userid = ($userid != 0) ? $userid : PROFILE['userid'];

		// Инцилизирует пользователя 
    	$this
			->priv
			->initRoles($this->userid);
	}
   
    function getAllPerms() {

		// Возвращает все привелегии пользователя
    	return $this
    				->priv
    				->getPerms();
    }

    function getPermsOfUser(): ?array {

    	$allPerms = $this->getAllPerms();

    	return array('права не установленны');
    }

    // При условии, что есть хоть одна привелегия возвращаем true

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