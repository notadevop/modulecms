<?php 
/**
 *
 *  Класс для разрещения проблем с ссылками URL и URI.
 *  Решение конфликтных или приоритетных ситуаций, вывод ссылок
 */

class Urlfixer {

    public function __construct() { }

    /**
     * // /search/something/is/here/ -> Возвращает массив всех путей
     * // -> ['search', 'something', 'is', 'here']
     *
     *  Пример использования:
     *  $routes = $obj->getRoutes();
     *  if($routes[0] == 'search') { if($routes[1] == 'book') { echo 'clicked'; } }
     */
    public function defragmentUrl(string $url=''): ?array {

        if(!empty($url)) {
            $base_uri = $url;
        } else {
            $base_uri = '';// Получаем из глобальной переменной
        }

        $routeValues = array();
        $routes = explode('/', $base_uri);

        foreach ($routes as $route) {
            if (trim($route) != '') { 
                array_push($routeValues, $route); 
            }
        }
        return !empty($routeValues) ? $routeValues : null;
    }

    /**
     *  Разделить переданный URL на компоненты
     *  https://google.ru/index.php?var=123  array=>( https:, google,
     *  index.php?var=123); 
     */
        
    public static function splitUrl(string $url) {
        return preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY);
    }

    function generateUrl($genURL=false) {

        $host = HOST;
    }

}