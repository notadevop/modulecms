<?php

namespace LanguagePack;

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


class LanguagePack {

  function browserLang() {
    $browserLang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    return substr($browserLang[0], 0, 5);
  }

  function initilizeLanguage() {

     // Загрузка из
        // URL
        // Куки
        // Базы данных
  }

  // Добавляем новые языки

  function delegator() { }


  private $defLang = 'Ru-ru';
  private $LanguagesFolder = 'LangLibrary';
  private $langStore = Array();

  function loadLanguage(String $alterLang='') {

    $lang = empty($alterLang) ? $this->defLang : $alterLang;

    $langFolder = ROOTPATH.$this->LanguagesFolder;

    $langFileIndexes = Array(
      'UILanguage.lang.php'
    );

    $store = array();

    try {
      $attempts = 1;
      $filesLoaded = 0;

      while($attempts <= 1) {

        foreach ($langFileIndexes as $langFile) {

          $file = $langFolder.DS.$lang.DS.$langFile;

          if (!file_exists($file)) {
            if($lang == $this->defLang) {
              $attempts=2;
              throw new Exception('Language file not found!');
            } else {
              $lang = $this->defLang;
              $attempts++;
              break;
            }
          } else {
            $this->langStore[] = require_once ($file);
            $filesLoaded++;
          }
        }

        if(count($langFileIndexes) == $filesLoaded){
          break;
        }
      }
    } catch (Exception $e) {
      die($e->getMessage());
    }

    return $store;
  }

  function getLanguage(){

    return $this->langStore;
  }
}
