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

    private $defaultLang;

    function __construct(){ 

        $this->defaultLang = DEFLANGUAGE;
    }

    function initLangFromCookie():?string {

        return null;
    }

    function initLangFromBrowser():?string {

        return null;
    }

    function initLangFromUser():?string {

        return null;
    }

    function initLangSystem():?string {

        return null;
    }

    function verifyExistingLang(): bool {

    }


    // Метод который устанавливает язык который будет в данной сессии

    function initilizeLang(): string {

    }

}