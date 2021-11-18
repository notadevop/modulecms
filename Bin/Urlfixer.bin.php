<?php 
/**
 *
 *  Класс для разрещения проблем с ссылками URL и URI.
 *  Решение конфликтных или приоритетных ситуаций, вывод ссылок
 */

class Urlfixer {

    public function __construct() { }

    public static function getCurrentUri() {

        // Тут нужна фильтрация данных !!!!

        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basepath   = implode('/', array_slice(explode('/', $scriptName), 0, -1)) . '/';
        $uri        = substr($_SERVER['REQUEST_URI'], strlen($basepath));
        
        if (strstr($uri, '?')) { 
            $uri = substr($uri, 0, strpos($uri, '?')); 
        }
        
        return strtolower('/' . trim($uri, '/'));
    }

    /**
     * // /search/something/is/here/ -> Возвращает массив всех путей
     * // -> ['search', 'something', 'is', 'here']
     *
     *  Пример использования:
     *  $routes = $obj->getRoutes();
     *  if($routes[0] == 'search') { if($routes[1] == 'book') { echo 'clicked'; } }
     */
    public function getUriElement(string $url=''): ?array {

        $base_uri = !empty($url) ? $uri : parse_url($_SERVER['REQUEST_URI'])['path'];

        $values = array();
        $routes = explode('/', $base_uri);

        $filter = new Filter();

        foreach ($routes as $route) {
            if (trim($route) != '') { 
                $route = $filter->keepParams($route, ['words','numbers','specsym']);
                array_push($values, $route); 
            }
        }
        return $values;
    }

    /**
     *  Разделить переданный URL на компоненты
     *  https://google.ru/index.php?var=123  array=>( https:, google,
     *  index.php?var=123); 
     */
        
    public static function splitUrl(string $url) { // : ?array
        return preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY);
    }

    function generateUrl(array $uriParams, bool $genURL=false) {

        HOST.DS.http_build_query($uriParams); 
    }

}