<?php 


final class Main extends ViewRender {



	private function init(): void {

		

	}


	// Функция, точка входа 

	static function runScript(): bool {

		$obj = self;

		$obj->init();

		Router::initDefaultRoutes();


		
		



	}
}