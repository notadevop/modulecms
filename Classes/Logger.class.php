<?php 

/*
    
    Данный скрипт собирает все ошибки в controller'е и выводит их во view 

    NB! Не удалять!!!!!!
*/


final class Logger {


    // Статический конструктор
    static protected function __constructorStatic() { 

    	self::$alertKeys = array(

    		'warnings' 		=> true,
    		'attentions' 	=> true,
    		'information' 	=> true,
    		'success' 		=> true
    	);
    }

    // Вызов статического конструктора
    static protected function __getStatic() {

    	static $called = false;

    	if (!$called) { 
    		$called = true;
    		// static::${$name};
    		self::__constructorStatic();
    	}
    }

    static $alertKeys 	= array();
    static $alerts 		= array();

    public static function collectAlert(string $type, string $description) :void {

    	self::__getStatic();

    	if(array_key_exists($type, self::$alertKeys)) {

    		self::$alerts[$type][] = $description; 
    	} 
    }

    public static function alertKeyExist(string $type): bool {

        //debugger(self::$alertKeys);

        if (array_key_exists($type, self::$alertKeys)) {

            if (!empty(self::$alerts[$type]) && count(self::$alerts[$type]) > 0) {

                return true;
            }
        }
        return false;
    }

    public static function getAlerts(string $type):?array {

    	return !self::alertKeyExist($type) ? NULL : self::$alerts[$type];
    }
}