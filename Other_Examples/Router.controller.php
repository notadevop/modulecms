<?php

//namespace Controller\RouterController;

class Router {
	
	private $routes = array();
	private $params = array();

	function __construct($route){ 

		foreach ($route as $key => $params) {
			
			$this->addRoute($key, $params);
		}
	}

	function addRoute(string $route, array $params):void {

		if (!array_key_exists($route, $this->routes)) {

			$this->routes[$route] = $params;
		}
	}

	// Получаем список шаблонов и меняем в зависимости от исполнения контроллера 

	//$templates = array();

	public function runRouter(): void{

		$uri = trim($this->getCurrentUri(), '/');

		foreach ($this->routes as $route => $params) {

			//if (strpos($uri, $route) !== false) { }

			if (preg_match('#^'.$uri.'$#', $route) || $route === '__hidden_auth__') {

				$this->executeRouteController($this->routes[$route]);
			}
		}
	}

	private function executeRouteController(array $cont):void  {

		// Роутинг используется для исполнения для генерации шаблона 

		//$cont = $this->matchRoute();

		if (!$cont) { debugger('Путь не найден!'); }

		$myclass 	= $cont['controller'];
		$myObject 	= new $myclass();

		$args 		= array($cont['args']);
		$retval 	= call_user_func_array(array($myObject, $cont['action']), $args);

		$getErrors  = $myObject->getErrors(); // params

		debugger($getErrors, __METHOD__);
	}

	function getCurrentUri(){
		/*
			this router working only with this 
			Options -MultiViews
		    RewriteEngine On
		    RewriteCond %{REQUEST_FILENAME} !-f
		    RewriteRule ^ index.php [QSA,L]
		*/
		$scriptName = $_SERVER['SCRIPT_NAME'];
		$basepath 	= implode('/', array_slice(explode('/', $scriptName), 0, -1)) . '/';
		$uri 		= substr($_SERVER['REQUEST_URI'], strlen($basepath));

		if (strstr($uri, '?')) {

			$uri = substr($uri, 0, strpos($uri, '?'));
		}
		
		$uri = '/' . trim($uri, '/');
		return strtolower($uri);
	}

	function getRoutes(string $extUri=''): array {

		// /search/something/is/here/ -> Возвращает массив всех путей 
		// -> ['search', 'something', 'is', 'here']

		if (empty($extUri)) {  $base_uri = $this->getCurrentUri(); }

		$routeValues = array();
		$routes = explode('/', $base_uri);
		
		foreach($routes as $route) {

			if(trim($route) != '') {
			
				array_push($routeValues, $route);
			}
		}

		/** Пример использования: 
		$routes = $obj->getRoutes();
		if($routes[0] == 'search') {
			if($routes[1] == 'book') {
				echo 'clicked';
			}
		}*/
		return $routeValues;
	}


	function fixCurrentUri(string $uri): string {

		//$curUri = $this->getRoutes();

		//$replUri = $this->getRoutes('/auth/mycooluri');

		// получаем ссылку которую нужно установить
		// разделяем ее на составные 
		// получаем ссылку которая сейчас есть 
		// удаляем дубликаты и генерирует ту ссылку которую нужно установить

		return '';
	}
}