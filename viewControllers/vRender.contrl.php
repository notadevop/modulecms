<?php 
/**
 *  Отображает вывод из шаблона
 */

class vRender {

	
	private $result;

	private $currentTplDir;
	private $activeTpl;

	private $currentRoute;
	private $replaceParams;

	private $regOk;
	private $params;

	private $htmlRenderRes;

	private $pages;

	function __construct() {

		// Как то определить к какому пути относится какой шаблон

		Router::initDefaultRoutes();
		$this->result 		= Router::getResult();
		$this->currentRoute = Router::getRoute();

		$this->regOk = (defined('PROFILE') && !empty(PROFILE['useremail'])) ? true : false;

		// pages это массив для определения шаблона для определенных страниц 
		// например чтобы разные страницы имели разные шаблоны

		$this->pages = array(
			'user' 	=> false,
			'admin' => false,
			'auth' 	=> false,
		);

		$this->currentTplDir = TPLDEFAULTFOLDER;

		$settings = new HostSettings();

		$prm = array(
			'website_template' 			=> '', // Пользовательский шаблон
			'website_title'  			=> '', 
			'website_title_description' => '',
		);

		$this->params = $settings->getSettings($prm);
	}

	function activateTemplate(string $name, string $folder=''): ?array {

		$folder = !empty($folder) ? ROOTPATH.$folder.DS : $this->currentTplDir;
		
		$fpath = $folder.$name.DS.'schema.tpl.php';
		
		if (!file_exists($fpath)) { return null; }
		
		$tplarr = require_once($fpath);
		if(empty($tplarr)) { return null; }
		$this->activeTpl 					= $folder.$name.DS;
		$this->params['website_template'] 	= $name;
		return $tplarr;
	}


	function prepareRender() {

		$Allroutes  	= Router::getRoute(true); // Все пути 
		$currentRoute 	= $this->currentRoute;
		$ui 			= null;

		if(isset($currentRoute['uriarr'][0]) && !empty($currentRoute['uriarr'][0])){

			$ui = $currentRoute['uriarr'][0];
		}

		$deniedTpl = false;

		if($ui == 'admin' && $this->regOk) {
			$r = $this->activateTemplate('admin');
		} else {

			if($ui == 'admin' && !$this->regOk) {
				$deniedTpl = true;
			}

			$r = $this->activateTemplate($this->params['website_template']);
		}

		if (!$r) {
			$r = $this->activateTemplate(TPLDEFTEMPLATE);
		}

		if(empty($this->currentRoute)) {
			$defTpl = $Allroutes['/404page']['template'];
		}elseif ($deniedTpl){
			$defTpl = $Allroutes['/login']['template'];
		} else { 
			$defTpl = $Allroutes[$this->currentRoute['uri']]['template'];
		}

		if(!$r || !file_exists($this->activeTpl.$defTpl)) {

			die('Template Not Found -> '.$this->activeTpl.$defTpl);
		}

		// Тут определить какой тип страницы открыт. 
		// Админка, окно входа или пользовательский интерфейс

		// TODO: Определить язык пользователя и загрузить тот языковый пакет
		// 
		ob_start();
		if(isset($r['languagePack'][LANGUAGE])) {
			if (!file_exists($this->activeTpl.$r['languagePack'][LANGUAGE])) {
				Logger::collectAlert('warnings', 'Нет языкового пакета!');
			} else {
				require_once($this->activeTpl.$r['languagePack'][LANGUAGE]);
			}
		}
	
		require_once ($this->activeTpl.$defTpl);
		$this->htmlRenderRes = ob_get_contents();
		ob_end_clean();

		$replaceParams = array(
			' %title% ' 			=> 'Модульная CMS',
			' %sitetitle% ' 		=> $this->params['website_title'],
			' %site_description% ' 	=> $this->params['website_title_description'],
		);

		$this->replace($replaceParams);
	}

	function replace(array $params): void {
		if(empty($params)) return;
		$html = $this->htmlRenderRes;
		foreach ($params as $key => $value) {
			$html = str_replace($key, $value, $html);
		}
		$this->htmlRenderRes = $html;
	}

	function viewRender(): void {

		header('Content-Type: text/html; charset=utf-8');
		print($this->htmlRenderRes);
	}

	function __desctructor(){}
}