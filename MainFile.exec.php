<?php

namespace MainFile;

use \PDO;
use Debug\DebuggerСlass as DEBGR;
use LanguagePack\LanguagePack as LP;
use Database\Database as DB;
use Settings\SettingsController as SC;

use Globals\GlobalParams as Globus;

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

    $lang     = new LP();
    $settings = new SC();
    $database = new DB(true);

    $settings->loadSettings();
    $lang->loadLanguage();

    $database->setupSettings($settings->takeSettings()[0]['database']);
    $database->alertsLanguagePack($lang->getLanguage()[0]);
    //$database->make_con();

    Globus::initGlobalParams();

    print_r(Globus::getGlobalParams('GET'));



    $this->lang     = $lang;
    $this->settings = $settings;
    $this->database = $database;

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
