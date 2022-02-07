<?php 

/*

$arr = array('one', 'two', 'three');

try {
	
    $navBuild = new PageNavBuilder();
	$p = $navBuild->build(3, count($arr), 32);

	echo '<pre>';
	print_r($p);
	
    $navView = new PageNavView('/','comments');
	$navView->NavViewBuild($p);

} catch (Exception $e) {
	print($e->getMessage());
}

*/

DEFINE('NAVPAGEVIEW', 3);
DEFINE('SHOWNEXTNAVPAGE', true);
DEFINE('NAVBASEURL','/');



class PageNavBuilder {

    /**
     * Сколько показывать кнопок страниц до и после актуальной.
     *
     * Пример:
     * $spread = 5;
     * Всего 9 страниц навигации и сейчас просматривают 5ю
     * 1 ... 3 4 5 6 7 ... 9
     *
     * @var int
     */
    private $spread = NAVPAGEVIEW;

    /**
     * Номер просматриваемой страницы.
     *
     * @var int
     */
    private $currentPage = 0;
    /**
     * Определяет нужно ли показывать кнопки "Вперед" и "Назад".
     *
     * @var bool
     */
    private $nextPrev = SHOWNEXTNAVPAGE;

    /*
    function __construct() {

    }
    */
    /**
    *   Считает сколько нужно выводить элементов на страницу 
    *   к примеру страница 2 и 4 элемента, то результат вывода будет 
    *   от 5 - 9 го элемента 
    *   @var int $currentPage текушая страница на данный момент
    *   @var int $countAll количество всех записей в базе
    *   @var int $perPage Количество записей на данной странице 
    *   @return array Массив с количеством ids которые нужно отдать на вывод 
    */
    public function getListItemIds(int $perPage, int $countAll, int $currentPage = 1): array {

        $begin = 0;
        $end = 0;

        // TODO: проверить количество записей на страницу !!!

        // Основываться на id уникальной цифре

        // Добавляем каждой странице количество записей которое он должен вывести 
        // и останавливаемся на той странице которая указана 

        for ($i=1; $i < $currentPage; $i++) { 

            $begin  = $i == 1 ? 1 : $end; 
            $tmp    = $begin + $perPage;
            $end    = $tmp >= ($countAll) ? $countAll : $tmp;
        }

        return array('begin' => $begin, 'end' => $end); // возвращает диапазон 
    }


    /**
     * Строим навигации и формируем шаблон
     *
     * @param int $perPage количество записей на 1 страницу
     * @param int $countAll общее количество всех записей
     * @param int $currentPage номер просматриваемой страницы
     * @return mixed Сформированный шаблон навигации готовый к выводу
     */
    public function build(int $perPage, int $countAll, int $currentPage = 1):array {

        if ($perPage < 1 || $countAll <= $perPage) {
            throw new \RuntimeException('Incorrect per page value.');
        }

        $count_pages = ceil($countAll / $perPage);

        if ($currentPage < 1 || $currentPage > $count_pages) {
            throw new \RuntimeException('Incorrect current page number.');
        }

        $this->currentPage = $currentPage; // текущая страницв 

        // Странно !

        $shift_start    = max($this->currentPage - $this->spread, 2);
        $shift_end      = min($this->currentPage + $this->spread, $count_pages - 1);
        
        if ($shift_end < $this->spread * 2) {
            $shift_end = min($this->spread * 2, $count_pages - 1);
        }

        if ($shift_end == $count_pages - 1 && $shift_start > 3) {
            $shift_start = max(3, min($count_pages - $this->spread * 2 + 1, $shift_start));
        }

         // Получаем первую страницу

        //$list = $this->getItem(1);
        $list['1'] = 1; 

        if ($shift_start == 3) {
            //$list .= $this->getItem(2); // Вторую страницу
            $list['2'] = 2;
        } elseif ($shift_start > 3) {
            //$list .= $this->separator;
            $list['separator_back'] = true;
        }
        
        // все остальные, в том числе и те, что активные 

        for ($i = $shift_start; $i <= $shift_end; $i++) {
            //$list .= $this->getItem($i);
            $list["$i"] = "$i";
        }
        
        // Получаем последнюю страницу 
        $last_page = $count_pages - 1;
        
        if ($shift_end == $last_page - 1) {
            //$list .= $this->getItem($last_page);
            $list["$last_page"] = $last_page;
        } elseif ($shift_end < $last_page) {
            //$list .= $this->separator;
            $list['separator_front'] = true;
        }

        //$list .= $this->getItem($count_pages);

        $list['all_pages'] = $count_pages;
       
        if ($this->nextPrev) {

            $one = $this->currentPage > 1 ? $this->currentPage - 1 : 1;
            $two = $this->currentPage < $count_pages ? $this->currentPage + 1 : $count_pages;

            $list['before'] = $one;
            $list['next']   = $two;

            //$list = $this->getItem($one, $this->prevTitle).$list.$this->getItem($two, $this->nextTitle);
        }

        return $list;

        // Должен вернуть список страниц и диапазон id тех элементов которы нужно показать на странице 
        //return str_replace("{pages}", $list, $this->wrap);
    }
}