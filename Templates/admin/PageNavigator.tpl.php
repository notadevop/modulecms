<?php 

defined('ROOTPATH') or die();

/*

	Выводит постраничную навигацию на какие то элементы 

*/


class PageNavView {

    //     -------- VIEW ----------

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
    private $baseUrl = '/';

    /**
     * Шаблон обертки кнопок.
     *
     * @var string
     */
    private $wrap = '<div class="navigation">{pages}</div>';

    /**
     * Разрыв между номерами страниц.
     *
     * @var string
     */
    private $separator = ' <i>...</i> ';
    /**
     * Css класс элемента активной страницы.
     *
     * @var string
     */
    private $activeClass = 'link_active';

    /**
     * Текст кнопки "Назад".
     *
     * @var string
     */
    private $prevTitle = 'Назад';
    /**
     * Текст кнопки "Вперед".
     *
     * @var string
     */
    private $nextTitle = 'Вперед';
    /**
     * Шаблон ссылки навигации.
     *
     * @var string
     */
    private $tpl = 'page/{page}/';
    /**
     * Инициализация класса
     *
     * @param string $baseUrl URL в конец которого будет добавляться навигация
     */
    public function __construct(string $baseUrl = '/',string $pageTpl='') {

        $this->baseUrl = $baseUrl;
        // Условие если пользователь хочет изменить постраничную навигацию или использовать несколько 
        // несколько навигаций на одной странице. к примеру коментарии и еще какой то вывод данных 
        // которые подразумевает разбиение на страницы.
        if (!empty($pageTpl)) {
            $this->tpl = str_replace('page/', $pageTpl, $this->tpl).'{page}/'; 
        }
    }

    /**
     * Формирование адреса
     *
     * @param int $pageNum номер страницы
     * @return string
     */
    private function getUrl($pageNum = 0) {
        
        $page = $pageNum > 1 ? str_replace('{page}', $pageNum, $this->tpl) : '';

        return (stripos($this->baseUrl, '{page}') !== false) ? str_replace('{page}', $page, $this->baseUrl) : $this->baseUrl . $page;
        /*
        if (stripos($this->baseUrl, '{page}') !== false) {
            return str_replace('{page}', $page, $this->baseUrl);
        } else {
            return $this->baseUrl . $page;
        }
        */
    }

    public function NavViewBuild(array $navlist):void { 

        if (count($navlist) < 1 || empty($navlist)) {
            throw new \RuntimeException('Incorrect current page number.');
        }

        $build = '';

        foreach ($navlist as $key => $value) {
            if ($key == 'separator_back' || $key == 'separator_front') {
                $build .= $this->separator;
            } elseif ($key == 'before') {
                $build .= $this->getItem($value, $this->prevTitle);
            } elseif ($key == 'next') {
                $build .= $this->getItem($value, $this->nextTitle);
            } else {
                $build .= $this->getItem($value);
            }
        }

        $build = str_replace("{pages}", $build, $this->wrap);
        echo $build;
    }

    /**
     * Формирование кнопки/ссылки
     *
     * @param int $pageNum номер страницы
     * @param string $pageName если указано, будет выводиться текст вместо номера страницы
     * @return string
     */
    private function getItem($pageNum, $pageName = '') {

        $pageName = $pageName ?: $pageNum;
        if ($this->currentPage == $pageNum) {
            return " <strong><span class=\"{$this->activeClass}\">{$pageName}</span></strong> ";
        }
        return " <a href=\"{$this->getUrl($pageNum)}\">{$pageName}</a> ";
    }
}