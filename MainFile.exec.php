<?php

namespace MainFile;


use Debug\DebuggerСlass as DEBGR;
use LanguagePack\LanguagePack as LP;
use Database\Database as DB;
use \PDO;

use Settings\SettingsController as SC;

class MainExecutor {

  const ROUTE_INDEX_FILE='.htaccess';

  private $loadTime;

  function __construct() {

    $this->loadTime = new DEBGR();
    $this->loadTime->startTimer();

    $data = '
      Options -MultiViews
      RewriteEngine On
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteRule ^ index.php [QSA,L]
    ';

    if (!file_exists(ROOTPATH . self::ROUTE_INDEX_FILE)) {
      file_put_contents(self::ROUTE_INDEX_FILE, $data);
    }
  }

  private $lang;
  private $settings;
  private $database;


  function initCoreSettings() {

    $this->lang     = new LP();
    $this->settings = new SC();
    $this->database = new DB(true);

    $this->settings->loadSettings();
    $this->lang->loadLanguage();

    $this->database->authParams($this->settings->getSettings()[0]['database']);
    $this->database->setAlerts($this->lang->getLanguage()[0]);
    $this->database->make_con();

  }




  function showView() {

    DEBGR::debugger(strtoupper('Ошибка рендеринга!'));

    /*
    $v = new vRender();
    $v->prepareRender();

    $v->replace(
      array(
        '%memused%'		=> 'Использованная память: '.convert(memory_get_usage(true)),
        '%loadtime%' 	=> $this->stopTimer(),
      )
    );


    $v->viewRender();
    */
  }
}
