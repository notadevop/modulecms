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

final class Router {

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

	// Метод который модифицирует пути к спец, страницам 
	// например к аdmin переименновать в administrator
	// 
	// 	  нужно исполнять в константе чтобы можно было использовать 
	// 	  в где то еще 
	// 	  
	// 	  Использовать только для одной константы определения
	// 

	public static function modifyRoutes(string $page): string {

		if (empty(self::$defaultRoutes)) {
			die('<h1>'.NOROUTES.'</h1>');
		}

		if(empty($page)) {
			die('<h1>fatal! route affected!</h1>');
		}	

		$stg = new HostSettings();

		$tmp = $stg->getSettings([$page=>$page]);

		if(!$tmp || $tmp[$page] == $page) { return $page; }

		foreach (self::$defaultRoutes as $key => $value) {

			// Тут устанавливаем новый путь

			$newKey = str_ireplace(DS.$page, DS.$tmp[$page], $key);

			self::$defaultRoutes[$newKey] = $value;

			// Тут проверяем и удаляем старый взамен нового

			$first = Urlfixer::splitUrl($key);

			if (!empty($first) && $first[0] == $page) {

				unset(self::$defaultRoutes[$key]);
			}
		}
		return $tmp[$page];
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
			if ( preg_match('#^' .$regex. '$#', Urlfixer::getCurrentUri()) ) {

				$path = array(
					'uriarr' 	=> Urlfixer::splitUrl($key),
					'uri' 		=> $key,
					'action' 	=> Urlfixer::splitUrl($controller['action']),
					'params'	=> self::$defaultRoutes[$key]
				);
				return $path;
		  	}
		}
		return null;
	}

	/**
	 * отправляем url и получаем результат, но до этого разбиваем урл и сравниваем с путями установлеными в системе
	 */
	public static function dispatch($requestedUrl = null) {

		// Если URL не передан, берем его из REQUEST_URI
		if ($requestedUrl === null) {
			$cur = Urlfixer::getCurrentUri();
			$requestedUrl = $cur == '/' ? '/' : urldecode(rtrim($cur, '/'));
		}

		self::$requestedUrl = $requestedUrl;

		// если URL и маршрут полностью совпадают
		if (isset(self::$defaultRoutes[$requestedUrl])) {
			self::$params = Urlfixer::splitUrl(self::$defaultRoutes[$requestedUrl]['action']); // $requestedUrl
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

					self::$params = Urlfixer::splitUrl($uri['action']); // разбиваем value роута на параметры и сохраняем
					break; // URL обработан!
				}
			}
		}
		// Возвращает результат отработанно

		$controller = isset(self::$params[0]) ? self::$params[0] : 'MainController';
		$action 	= isset(self::$params[1]) ? self::$params[1] : 'defaultMethod';
		$params 	= array_slice(self::$params, 2);
		$obj 		= new $controller();


		return call_user_func_array(array($obj, $action), $params);
		/*
		return array(
			'result' => call_user_func_array(array($obj, $action), $params),
		);
		*/
	}

	// Метод достает все пути сохраненные в переменной self::defaultRoutes
	// 1. Отрабатывает все пути которые не имеют URI, постоянные типа авторизации 
	// 2. Отрабатывает один путь который указан в URI 
	// 3. Возвращает результат постоянных исполнителей в виде массива $result 
	// 4. И путь по которому зашел пользователь self::dispatch()
	


	public static function getPermanentResult(): ?array {

		$result = array();
		foreach (self::$defaultRoutes as $key => $value) {
			if(substr($key, 0, 1) != '/') {
				$result[$key] = self::dispatch($key);
				//self::cleanRoutes($key);
			}
		}

		return $result;
	}


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
			
			'templateRes' 	=> self::dispatch(), // Результат от пути 
			'permanetRes'	=> $result 			 // Результат перманент.
		);
	}
}


















