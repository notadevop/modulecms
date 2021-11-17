<?php 

/**
 *  Файл настроек по умолчанию, Используется для проверки настроек с базы данных
 */

class Settings {

    public function __construct() { }

    // TODO: определить приоритет настроек, и выводить тот вариант, что нужно 
    // у одних вариант с базы данных важнее настроек в файле, у других на оборот
    // 


    public function getCurrentStg(string $name, bool $ignorebd=false): void {

    	$stg = new HostSettings();

    }




    

}