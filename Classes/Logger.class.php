<?php 

/*
    
    Данный скрипт собирает все ошибки в controller'е и выводит их во view 

    NB! Не удалять!!!!!!
*/


final class Logger {

    const PRIMARY       = 'primary';
    const SECONDARY     = 'secondary';
    const SUCCESS       = 'success';
    const ATTENTIONS    = 'attentions'; 
    const WARNING       = 'warning';
    const INFORMATION   = 'information';
    const LIGHT         = 'light';
    const DARK          = 'dark';


    // Статический конструктор
    static protected function __constructorStatic() { 

    	self::$alertKeys = array(

            self::PRIMARY     => false,
            self::SECONDARY   => false,
            self::SUCCESS     => false,
            self::ATTENTIONS  => false,
            self::WARNING     => false,
            self::INFORMATION => false,
            self::LIGHT       => false,
            self::DARK        => false,
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

            if(isset(self::$alerts[$type]) && in_array($description, self::$alerts[$type])) {
                return;
            }

    		self::$alerts[$type][] = $description; 
    	} 
    }

    public static function alertKeyExist(string $type): bool {

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