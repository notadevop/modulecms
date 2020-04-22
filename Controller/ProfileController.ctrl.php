<?php 


class ProfileController {

    function __construct() {

    	$this->granter = new PrivelegesController();
    }

    private $granter;

    function getListOfUsers()  {

    	$perms = array(

    		$this
    			->granter
    			->getAllPerms()[0]['perm_desc'],
    		$this
    			->granter
    			->getAllPerms()[1]['perm_desc'],
    	);

	    if (!$this->granter->verifyRest($perms)) {
	    	
	    	echo '<p><b>forbidden! you dont have permission to access this webage!</b></p>';
	    	
	    	return false;
	    } 

	    echo "<p><b>You are logged with this permission!</p></b>";
	 }
}