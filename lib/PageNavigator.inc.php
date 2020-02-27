<?php 


class PaginateNavigationBuilder
{
    /**
     * Чистый URL по умолчанию.
     *
     * В адресе может быть указано место для размещения блока с номером страницы, тег {page}
     * Пример:
     * /some_url{page}.html
     * В итоге адрес будет:
     * /some_url.html
     * /some_url/page_2.html
     * Если тег {page} не указан, то страницы будут дописываться в конец адреса
     *
     * @var string
     */
    public $baseUrl = '/';
    /**
     * Шаблон ссылки навигации.
     *
     * @var string
     */
    public $tpl = 'page/{page}/';
    /**
     * Шаблон обертки кнопок.
     *
     * @var string
     */
    public $wrap = '<div class="navigation">{pages}</div>';
    /**
     * Сколько показывать кнопок страниц до и после актуальной.
     *
     * Пример:
     * $spread = 2
     * Всего 9 страниц навигации и сейчас просматривают 5ю
     * 1 ... 3 4 5 6 7 ... 9
     *
     * @var int
     */
    public $spread = 5;
    /**
     * Разрыв между номерами страниц.
     *
     * @var string
     */
    public $separator = '<i>...</i>';
    /**
     * Css класс элемента активной страницы.
     *
     * @var string
     */
    public $activeClass = 'link_active';
    /**
     * Номер просматриваемой страницы.
     *
     * @var int
     */
    public $currentPage = 0;
    /**
     * Определяет нужно ли показывать кнопки "Вперед" и "Назад".
     *
     * @var bool
     */
    public $nextPrev = true;
    /**
     * Текст кнопки "Назад".
     *
     * @var string
     */
    public $prevTitle = 'Назад';
    /**
     * Текст кнопки "Вперед".
     *
     * @var string
     */
    public $nextTitle = 'Вперед';
    /**
     * Инициализация класса
     *
     * @param string $baseUrl URL в конец которого будет добавляться навигация
     */
    public function __construct($baseUrl = '/')
    {
        $this->baseUrl = $baseUrl;
    }
    /**
     * Строим навигации и формируем шаблон
     *
     * @param int $perPage количество записей на 1 страницу
     * @param int $countAll общее количество всех записей
     * @param int $currentPage номер просматриваемой страницы
     * @return mixed Сформированный шаблон навигации готовый к выводу
     */
    public function build($perPage, $countAll, $currentPage = 1) {

        if ($perPage < 1 || $countAll <= $perPage) {
            throw new \RuntimeException('Incorrect per page value.');
        }

        $count_pages = ceil($countAll / $perPage);

        if ($currentPage < 1 || $currentPage > $count_pages) {
            throw new \RuntimeException('Incorrect current page number.');
        }

        $shift_start = max($this->currentPage - $this->spread, 2);
        $shift_end = min($this->currentPage + $this->spread, $count_pages - 1);
        
        if ($shift_end < $this->spread * 2) {
            $shift_end = min($this->spread * 2, $count_pages - 1);
        }
        if ($shift_end == $count_pages - 1 && $shift_start > 3) {
            $shift_start = max(3, min($count_pages - $this->spread * 2 + 1, $shift_start));
        }
        $list = $this->getItem(1);

        if ($shift_start == 3) {
            $list .= $this->getItem(2);
        } elseif ($shift_start > 3) {
            $list .= $this->separator;
        }
        
        for ($i = $shift_start; $i <= $shift_end; $i++) {
            $list .= $this->getItem($i);
        }
        
        $last_page = $count_pages - 1;
        
        if ($shift_end == $last_page - 1) {
            $list .= $this->getItem($last_page);
        } elseif ($shift_end < $last_page) {
            $list .= $this->separator;
        }

        $list .= $this->getItem($count_pages);
       
        if ($this->nextPrev) {
            $list = $this->getItem(
                    $this->currentPage > 1 ? $this->currentPage - 1 : 1,
                    $this->prevTitle
                )
                . $list
                . $this->getItem(
                    $this->currentPage < $count_pages ? $this->currentPage + 1 : $count_pages,
                    $this->nextTitle
                );
        }
        return str_replace("{pages}", $list, $this->wrap);
    }
    /**
     * Формирование адреса
     *
     * @param int $pageNum номер страницы
     * @return string
     */
    private function getUrl($pageNum = 0) {
        
        $page = $pageNum > 1 ? str_replace('{page}', $pageNum, $this->tpl) : '';

        if (stripos($this->baseUrl, '{page}') !== false) {
            return str_replace('{page}', $page, $this->baseUrl);
        } else {
            return $this->baseUrl . $page;
        }
    }
    /**
     * Формирование кнопки/ссылки
     *
     * @param int $pageNum номер страницы
     * @param string $pageName если указано, будет выводиться текст вместо номера страницы
     * @return string
     */
    private function getItem($pageNum, $pageName = '')
    {
        $pageName = $pageName ?: $pageNum;
        if ($this->currentPage == $pageNum) {
            return "<span class=\"{$this->activeClass}\">{$pageName}</span>";
        }
        return "<a href=\"{$this->getUrl($pageNum)}\">{$pageName}</a>";
    }
}