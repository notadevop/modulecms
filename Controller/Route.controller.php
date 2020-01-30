<?php

/**
 * 
 */
class Router {
	
	function __construct() { }


	function get_current_uri(){
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

		if (strstr($uri, '?')) 
			$uri = substr($uri, 0, strpos($uri, '?'));
		
		$uri = '/' . trim($uri, '/');
		return $uri;
	}

	function find_a_route() 
	{
		/** Пример: 
		$routes = $obj->getRoutes();
		if($routes[0] == 'search') {
			if($routes[1] == 'book') {
				echo 'clicked';
			}
		} */

		$base_url = getCurrentUri();
		$routeValues = array();
		$routes = explode('/', $base_url);
		
		foreach($routes as $route) {
			if(trim($route) != '') 
				array_push($routeValues, $route);
		}
		// /search/something/is/here/ -> Возвращает массив всех путей 
		// -> ['search', 'something', 'is', 'here']
		return $routeValues;
	}

}