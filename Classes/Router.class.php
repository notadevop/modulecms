<?php 

/**
*	Класс отработки путей веб сайта
*
*/

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
	  '/'               => 'BlogController/index', // главная страница
	  '/contacts'       => 'BlogController/contacts', // страница контактов
	  '/blog'           => 'BlogController/index', // список постов блога
	  '/blog/:num'      => 'BlogController/viewPost/$1' // просмотр отдельного поста, например, /blog/123
	  '/blog/:any/:num' => 'BlogController/$1/$2' // действия над постом, например, /blog/edit/123 или /blog/dеlete/123
	  '/:any'           => 'BlogController/anyAction' // все остальные запросы обрабатываются здесь
	));

	// добавляем все маршруты за раз
	Routing::addRoute($routes);

	// а можно добавлять по одному
	Routing::addRoute('/about', 'MainController/about');  <== изменено используется просто массив со всем параметрами

	// непосредственно запуск обработки
	Routing::dispatch();

	// для нормального использования роутера 
	// нужно добавить в .htaccess параметры  

	Options -MultiViews
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [QSA,L]
	*/


	/**
	 * // /search/something/is/here/ -> Возвращает массив всех путей
	 * // -> ['search', 'something', 'is', 'here']
	 *
	 *  Пример использования:
	 *	$routes = $obj->getRoutes();
	 *	if($routes[0] == 'search') { if($routes[1] == 'book') { echo 'clicked'; } }
	 
	public static function getRoutes(string $extUri = ''): ?array {

		$base_uri = empty($extUri) ? self::getCurrentUri() : $extUri;
		$routeValues = array();
		$routes = explode('/', $base_uri);

		foreach ($routes as $route) {
			if (trim($route) != '') { array_push($routeValues, $route); }
		}
		return !empty($routeValues) ? $routeValues : null;
	}
	*/


final class Router {

	//private static 	$routes 			= array();
	private static 	$params 			= array();
	private static 	$requestedUrl;
	public static 	$defaultRoutesDir 	= ROOTPATH.DEFROUTEPATH;
	public static 	$defaultRoutes 		= array();



	// Инициализирует все пути по умолчанию
	// Которые не обходимы для базовой работы 
	// Если в пути не найдены убиваем процесс die();

	public static function initDefaultRoutes(): void{

		$fileObj 	= new Filemanipulator();
		$fileObj->setDirName(self::$defaultRoutesDir);
		$files 		= $fileObj->listFolder();

		if(count($files) < 1 || !($files = preg_grep('/.route.php/i', $files))) {
			die('<h1>'.NOROUTES.'</h1>');
		}

		foreach ($files as $key => $value) {
			$result = self::$defaultRoutesDir.$value;
			if (!file_exists($result)) { continue; }
			$result = require_once $result;
			if (is_array($result) && count($result) > 0) {
				self::addRoute($result);
			}
		}
	}

	// Добавить новый путь

	public static function addRoute(array $route): void {
		if (empty($route) || !is_array($route)) { return; }
		$newArr = array();
		foreach ($route as $key => $value) {
			if (!array_key_exists($key, self::$defaultRoutes)) {
				$newArr[$key] = $value;
			}
		}
		if (count($newArr) < 1) { return; }
		self::$defaultRoutes = array_merge(self::$defaultRoutes, $newArr);
	}

	/**
	 * Разделить переданный URL на компоненты
	 	https://google.ru/index.php?var=123  array=>( https:, google, index.php?var=123); 
	 */
	public static function splitUrl(string $url) {
		return preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY);
	}

	// Очищаем путь роутера

	public static function cleanRoute(string $routeName): bool {
		if (array_key_exists($routeName, self::$defaultRoutes)) {
			unset(self::$defaultRoutes[$routeName]);
			return true;
		}
		return false;
	}


	// Метод возвращает массив с параметрами одного пути
	// Либо все сохраненные пути 

	public static function getRoute(bool $getAllRoutes=false): ?array {

		if ($getAllRoutes) {return self::$defaultRoutes;}

		foreach(self::$defaultRoutes as $key => $controller) {
			$regex = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', $key));
			if ( preg_match('#^' .$regex. '$#', self::getCurrentUri()) ) {

				$path = array(
					'uriarr' 	=> self::splitUrl($key),
					'uri' 		=> $key,
					'action' 	=> self::splitUrl($controller['action']),
					'params'	=> self::$defaultRoutes[$key]
				);
				return $path;
		  	}
		}
		return null;
	}


	public static function getCurrentUri() {
		$scriptName = $_SERVER['SCRIPT_NAME'];
		$basepath 	= implode('/', array_slice(explode('/', $scriptName), 0, -1)) . '/';
		$uri 		= substr($_SERVER['REQUEST_URI'], strlen($basepath));
		if (strstr($uri, '?')) { 
			$uri = substr($uri, 0, strpos($uri, '?')); 
		}
		return strtolower('/' . trim($uri, '/'));
	}

	/**
	 * отправляем url и получаем результат, но до этого разбиваем урл и сравниваем с путями установлеными в системе
	 */
	public static function dispatch($requestedUrl = null) {

		// Если URL не передан, берем его из REQUEST_URI
		if ($requestedUrl === null) {
			$cur = self::getCurrentUri();
			$requestedUrl = $cur == '/' ? '/' : urldecode(rtrim($cur, '/'));
		}

		self::$requestedUrl = $requestedUrl;

		// если URL и маршрут полностью совпадают
		if (isset(self::$defaultRoutes[$requestedUrl])) {
			self::$params = self::splitUrl(self::$defaultRoutes[$requestedUrl]['action']); // $requestedUrl
		} else { 
			foreach (self::$defaultRoutes as $route => $uri) {
				// Заменяем wildcards на рег. выражения
				if (strpos($route, ':') !== false) {
					$route = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', $route));
				}

				if (preg_match('#^' . $route . '$#', $requestedUrl)) { // $requestedUrl
					if (strpos($uri['action'], '$') !== false && strpos($route, '(') !== false) {
						$uri['action'] = preg_replace('#^' . $route . '$#', $uri['action'], $requestedUrl);
					}

					self::$params = self::splitUrl($uri['action']); // разбиваем value роута на параметры и сохраняем
					break; // URL обработан!
				}
			}
		}
		// Возвращает результат отработанно

		$controller = isset(self::$params[0]) ? self::$params[0] : 'MainController';
		$action 	= isset(self::$params[1]) ? self::$params[1] : 'defaultMethod';
		$params 	= array_slice(self::$params, 2);
		$obj 		= new $controller();

		return array(
			'result' => call_user_func_array(array($obj, $action), $params),
		);
	}

	// Метод достает все пути сохраненные в переменной self::defaultRoutes
	// 1. Отрабатывает все пути которые не имеют URI, постоянные типа авторизации 
	// 2. Отрабатывает один путь который указан в URI 
	// 3. Возвращает результат постоянных исполнителей в виде массива $result 
	// 4. И путь по которому зашел пользователь self::dispatch()

	public static function getResult() {
		$result = array();
		foreach (self::$defaultRoutes as $key => $value) {
			
			if(substr($key, 0, 1) != '/') {
				$result[$key] = self::dispatch($key);
				//self::cleanRoutes($key);
			}
		}

		return array(
			// Отрабатывает по указанному пути
			//'tplControllerResult' => self::dispatch(),
			//'perControllerResult' => $result

			'templateCtrlResult' 	=> self::dispatch(), // Результат от пути 
			'permanetCtrlResult'	=> $result 			 // Результат перманент.
		);
	}
}


















