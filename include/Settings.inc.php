<?php 

/**
 *  Файл настроек по умолчанию, Используется для проверки настроек с базы данных
 */


/**
 * summary
 */
class Settings {

	private $hstg;

    public function __construct() { 

    	$this->hstg = new HostSettings();
    }

    // TODO: определить приоритет настроек, и выводить тот вариант, что нужно 
    // у одних вариант с базы данных важнее настроек в файле, у других на оборот

    public function getSettings(string $name): void {

    }




    // Тут настройки вебсайта по умолчанию
    
    const DEFAUlTLANG 	= 'RU';
    const ENABLEHOST 	= true; 


    // Авторизация 
    
    const ALLOWAUTHENTICATE = true;
    const ALLOWREGISTRATION = true;
    const ALLOWLOGIN		= true;
    const ALLOWRESTORE 		= true;

    // Пути 
    

}