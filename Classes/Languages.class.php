<?php 

/**
 *
 * 
 */



/*
    
    проверка языка который указывает пользователь
    если нет ничего, тогда смотрим на установленные 
    в куки сохраненные параметры, 
    если там пусто, достаем из браузера язык, 
    проверяем если языковый пакет, если его нет достаем
    установленный по умолчанию язык 

    возможные языки которые можно показать сканируем в папке с языками 
    и по ним уже выводим возможные варианты!

*/


class Languages {

    private $defLang;
    private $curLang;
    private $languages; 

    public function __construct(){ 

    	$l = new Visitor();

    	$this->curLang 		= $l->getLang();
    	$this->languages 	= array();
    	$this->defLang 		= DEFLANGUAGE;
    }


    function initLangFromCookie() {

    }

    function initLangFromBrowser() {

    }

    function initLangFromUser() {

    }

    function initLangSystem() {

    }


}