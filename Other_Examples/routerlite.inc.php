<?php
/**
 * Простой роутер
 * @devg
 */
final class RouterLite {

  public  static $routes      = array();
  private static $params      = array();
  public  static $requestedUrl= '';

  /**
   * Добавить маршрут
   */
  public static function addRoute($route, $destination=null) {

    if ($destination != null && !is_array($route)) {

      $route = array($route => $destination);
    }
    self::$routes = array_merge(self::$routes, $route);
  }

  /**
   * Разделить переданный URL на компоненты
   */
  public static function splitUrl($url) {

    return preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY);
  }
  
  /**
   * Текущий обработанный URL
   */    
  public static function getCurrentUrl() {

    return (self::$requestedUrl?:'/');
  }

  /**
   * Обработка переданного URL
   */
  public static function dispatch($requestedUrl = null) {

      // Если URL не передан, берем его из REQUEST_URI
      if ($requestedUrl === null) {

        $uri = reset(explode('?', $_SERVER["REQUEST_URI"]));
        $requestedUrl = urldecode(rtrim($uri, '/'));
      }

      self::$requestedUrl = $requestedUrl;

      // если URL и маршрут полностью совпадают
      if (isset(self::$routes[$requestedUrl])) {

        self::$params = self::splitUrl(self::$routes[$requestedUrl]);
        return self::executeAction();
      }

      foreach (self::$routes as $route => $uri) {

        // Заменяем wildcards на рег. выражения
        if (strpos($route, ':') !== false) {
          
          $route = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', $route));
        }

        if (preg_match('#^'.$route.'$#', $requestedUrl)) {
          
          if (strpos($uri, '$') !== false && strpos($route, '(') !== false) {
            
            $uri = preg_replace('#^'.$route.'$#', $uri, $requestedUrl);
          }
          
          self::$params = self::splitUrl($uri);
            
          break; // URL обработан!
        }
      } 
      return self::executeAction();
  } 

 /**
   * Запуск соответствующего действия/экшена/метода контроллера
   */
  public static function executeAction() {

    $controller = isset(self::$params[0]) ? self::$params[0]: 'DefaultController';
    $action = isset(self::$params[1]) ? self::$params[1]: 'default_method';
    $params = array_slice(self::$params, 2);

    return call_user_func_array(array($controller, $action), $params);
  }

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
?>
