<?php

final class Routing {

	public static $routes = array();
	private static $params = array();
	public static $requestedUrl = '';

	/**
	 * Добавить маршрут
	 */
	public static function addRoute($route, $destination = null) {

		if ($destination != null && !is_array($route)) {

			$route = array($route => $destination);
		}
		self::$routes = array_merge(self::$routes, $route);
	}

	/**
	*	Загружает все дефольтные роуты из папки Routes
	*/
	public static function initDefRoutes(): ?array {

		$fm = new Filemanipulator();
		$fm->setDirName( ROOTPATH . DEFROUTEPATH);

		$files = $fm->listFolder();

		$exiting = function () {

			die('Пути не найдены! Обратитесь к администратору для решения проблемы.');
		};

		// Использ. preg_grep($key, $array);
		// Использ. array_walk()
		// Использ. array_filter() испльз. сallback function

		if (!$files || count($files) == 0) { $exiting(); }

		$files = preg_grep('/.route.php/i', $files);

		if (empty($files)) {  $exiting(); }

		$loadedRoutes = array();

		foreach ($files as $key => $value) {
			
			$path = ROOTPATH . DEFROUTEPATH . $value;

			if (file_exists($path)) {

				require_once $path;

				if (isset($routes) && !empty($routes)) {

					$loadedRoutes = array_merge($loadedRoutes, $routes);

					unset($routes);
				}
			}
		}

		return $loadedRoutes;
	}


	public static function showRoutes() {

		debugger(self::$routes, __METHOD__);
	}

	/**
	 * Разделить переданный URL на компоненты
	 */
	public static function splitUrl(string $url) {

		return preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY);
	}

	//  ВОЗВРАЩАЕТ ПОЛНЫЙ ОБРЕЗАННЫЙ URI не URL 
	public static function getCurrentUrl() {

		return (self::$requestedUrl ?: '/');
	}

	/**
	*	Очищаем пути 
	*/
	public static function cleanRoutes(string $arraykey) {

		if (array_key_exists($arraykey, self::$routes)) {

			unset(self::$routes[$arraykey]);
		}
	}

	public static function getNameOfRoute() {

		$routes = self::$routes;

		foreach($routes as $reg => $controller) {

			$regex = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', $reg));

			if ( preg_match('#^' .$regex. '$#', self::getCurrentUrl()) ) {
		    

		    	return array('uri' => $reg, 'params' => $controller);
		  	}
		}
		return false;
	}

	/**
	 * Обработка переданного URL
	 */
	public static function dispatch($requestedUrl = null) {

		// Если URL не передан, берем его из REQUEST_URI
		if ($requestedUrl === null) {

			$cur = self::getCurrentUri();
			$requestedUrl = $cur == '/' ? '/' : urldecode(rtrim($cur, '/'));
		}

		self::$requestedUrl = $requestedUrl;

		// если URL и маршрут полностью совпадают
		if (isset(self::$routes[$requestedUrl])) {

			self::$params = self::splitUrl(self::$routes[$requestedUrl]); // $requestedUrl
			return self::executeAction();
		}

		foreach (self::$routes as $route => $uri) {

			// Заменяем wildcards на рег. выражения
			if (strpos($route, ':') !== false) {

				$route = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', $route));
			}

			if (preg_match('#^' . $route . '$#', $requestedUrl)) { // $requestedUrl
				
				if (strpos($uri, '$') !== false && strpos($route, '(') !== false) {

					$uri = preg_replace('#^' . $route . '$#', $uri, $requestedUrl);
				}

				self::$params = self::splitUrl($uri); // ты мы разбиваем value роута на параметры и сохраняем
				break; // URL обработан!
			}
		}

		return self::executeAction();
	}

	public static function getCurrentUri() {

		$scriptName = $_SERVER['SCRIPT_NAME'];
		$basepath 	= implode('/', array_slice(explode('/', $scriptName), 0, -1)) . '/';
		$uri 		= substr($_SERVER['REQUEST_URI'], strlen($basepath));

		if (strstr($uri, '?')) {$uri = substr($uri, 0, strpos($uri, '?'));}

		return strtolower('/' . trim($uri, '/'));
	}

	/**
	 * // /search/something/is/here/ -> Возвращает массив всех путей
	 * // -> ['search', 'something', 'is', 'here']
	 *  Пример использования:
	 *	$routes = $obj->getRoutes();
	 *	if($routes[0] == 'search') {
	 *		if($routes[1] == 'book') {
	 *			echo 'clicked';
	 *		}
	 *	}
	 */
	public static function getRoutes(string $extUri = ''): array{

		$base_uri = empty($extUri) ? self::getCurrentUri() : $extUri;

		$routeValues = array();
		$routes = explode('/', $base_uri);

		foreach ($routes as $route) {

			if (trim($route) != '') {

				array_push($routeValues, $route);
			}
		}

		return empty($routeValues) ? array('') : $routeValues;
	}

	/**
	 * 	Запуск соответствующего действия/экшена/метода контроллера
	 */
	public static function executeAction() {

		$controller = isset(self::$params[0]) ? self::$params[0] : 'MainController';
		$action = isset(self::$params[1]) ? self::$params[1] : 'defaultMethod';
		$params = array_slice(self::$params, 2);

		$obj = new $controller();
		$cresult = call_user_func_array(array($obj, $action), $params);

		$params = array(
			'errors' => 'getErrors', 
			'notifs' => 'getNotif'
		);

		foreach ($params as $key => $value) {
			
			if(method_exists($controller, $value)) {

				$params[$key] = call_user_func(array($obj, $value));
			}
		}

		return array(
			'result' => $cresult,
			'errors' => $params['errors'],
			'notifs' => $params['notifs']	
		);
	}

	//  ------------------------------------
	//  +          пример использования
	//  ------------------------------------

	// маршруты (можно хранить в конфиге приложения)
	// можно использовать wildcards (подстановки):
	// :any - любое цифробуквенное сочетание
	// :num - только цифры
	// в результирующее выражение записываются как $1, $2 и т.д. по порядку
	/*
	$routes = array(
	  // 'url' => 'контроллер/действие/параметр1/параметр2/параметр3'
	  '/'               => 'MainController/index', // главная страница
	  '/contacts'       => 'MainController/contacts', // страница контактов
	  '/blog'           => 'BlogController/index', // список постов блога
	  '/blog/:num'      => 'BlogController/viewPost/$1' // просмотр отдельного поста, например, /blog/123
	  '/blog/:any/:num' => 'BlogController/$1/$2' // действия над постом, например, /blog/edit/123 или /blog/dеlete/123
	  '/:any'           => 'MainController/anyAction' // все остальные запросы обрабатываются здесь
	));

	// добавляем все маршруты за раз
	RouterLite::addRoute($routes);

	// а можно добавлять по одному
	RouterLite::addRoute('/about', 'MainController/about');

	// непосредственно запуск обработки
	RouterLite::dispatch();
	*/

	// ========== ДЛЯ НОРМАЛЬНОГО ИСПОЛЬЗОВАНИЯ РОУТЕРА НУЖНО ИСПОЛЬЗОВАТЬ ЭТО ======= //

	// ----> добавить в .htaccess

	/*
			this router working only with this
			Options -MultiViews
		    RewriteEngine On
		    RewriteCond %{REQUEST_FILENAME} !-f
		    RewriteRule ^ index.php [QSA,L]
	*/
}