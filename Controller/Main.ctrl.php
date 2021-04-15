<?php 

(defined('ROOTPATH') && defined('DS')) or die('something wrong!');

final class Main extends ViewRender {

	private function init(): void {

		Router::initDefaultRoutes();

		$result = Router::getResult();

		$host = new HostSettings();

		$this->initRenderSettings();
		$this->initSpecsPages();
		$this->replaceContent();
		$this->activateTemplate();
		$this->prepareRender();
		$this->ViewRender();
	}

	public static $initObj;

	// Функция, точка входа 

	static function runScript(): void {

		if(self::$initObj === null) {

            self::$initObj = new self();       
        }
        
        self::$initObj->init();

        /*
			$obj = self;
			$obj->init();
			Router::initDefaultRoutes();
		*/
	
		// table with requests 
	} 
}