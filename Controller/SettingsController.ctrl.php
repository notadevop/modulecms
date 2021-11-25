<?php 

/**
 * summary
 */
class SettingsController {

	private $getSettings;
	private $params; 
	private $paramRes;

    public function __construct() {
        
    	$this->params = array(

    		'website_template' 			=> false,
    		'website_title' 			=> '',
    		'website_title_description' => '',
    		'admin' 					=> '',
    		'login' 					=> '',
    		'login_status' 				=> LOGINALLOW,
    		'auth_status' 				=> AUTHALLOW,
    		'reg_status' 				=> REGISTRATIONALLOW,
    		'hostenabled' 				=> false, 
    		'HostName' 					=> false, 	
    	);

        $this->getSettings = new HostSettings();

        $this->paramRes = $this->getSettings->getSettings($this->params);

        //debugger($this->paramRes);
    }

    function initDefSettings() {


    }


    function updateSettings() {


    	return $this->paramRes;
    }

}