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
	private $allRoutes;


	function __construct() {

		// Как то определить к какому пути относится какой шаблон

		Router::initDefaultRoutes();
		define('ADMINPAGE', Router::modifyRoutes('admin'));


		$this->result 		= Router::getResult();
		$this->currentRoute = Router::getRoute();
		$this->allRoutes 	= Router::getAllRoutes(); // Все пути 

		$this->regOk = (defined('PROFILE') && !empty(PROFILE['useremail'])) ? true : false;

		// pages это массив для определения шаблона для определенных страниц 
		// например чтобы разные страницы имели разные шаблоны

		$this->pages = array(
			'user' 	=> false,
			'admin' => false,
			'auth' 	=> false,
		);

		$this->currentTplDir = TPLFOLDER;

		$settings = new HostSettings();

		// TODO: Сперва определить куда попал ?????

		$prm = array(
			'website_template' 			=> '', // Пользовательский шаблон
			'website_title'  			=> '', 
			'website_title_description' => '',
		);

		$this->params = $settings->getSettings($prm);
	}

	function activateTemplate(string $name, string $folder=''): ?array {

		$folder = !empty($folder) ? ROOTPATH.$folder.DS : $this->currentTplDir;
		
		$fpath = $folder.$name.DS.TPLSCHEMEFILE;
		
		if (!file_exists($fpath)) { return null; }
		
		$tplarr = require_once($fpath);
		if(empty($tplarr)) { return null; }
		$this->activeTpl 					= $folder.$name.DS;
		$this->params['website_template'] 	= $name;
		return $tplarr;
	}

	// Тут определить какая страница сейчас загруженна

	function identifyPage() {

	}


	function prepareRender() {

		$ui = null;

		if(!empty(isset($this->currentRoute['uriarr'][0]))){

			$ui = $this->currentRoute['uriarr'][0];
		}


		// Определяет нужно заблокировать вывод или на оборот
		// нужно например для административной части вывода вместо html json 

		$deniedTpl = false;

		if($ui == ADMINPAGE && $this->regOk) {
			$r = $this->activateTemplate('admin');
		} else {

			if($ui == ADMINPAGE && !$this->regOk) {
				$deniedTpl = true;
			}

			$r = $this->activateTemplate($this->params['website_template']);
		}

		// Тут устанавливается шаблон по умолчанию, 
		// если не найден другой!
		if (!$r) {
			$r = $this->activateTemplate(TPLTEMPLATE);
		}

		if(empty($this->currentRoute)) {
			$defTpl = $this->allRoutes['/404page']['template'];
		}elseif ($deniedTpl){
			$defTpl = $this->allRoutes['/login']['template'];
		} else { 
			$defTpl = $this->allRoutes[$this->currentRoute['url']]['template'];
		}


		ob_start();

		if(!$r || !file_exists($this->activeTpl.$defTpl)) {

			die(NOTEMPLETEFOUND.' -> '.$this->activeTpl.$defTpl);
		}


		if(isset($r['languagePack'][LANGUAGE])) {
			if (!file_exists($this->activeTpl.$r['languagePack'][LANGUAGE])) {
				Logger::collectAlert('warnings', 'Нет языкового пакета!');
			} else {
				require_once($this->activeTpl.$r['languagePack'][LANGUAGE]);
			}
		}

		try {
			if (!require_once ($this->activeTpl.$defTpl)) {
				throw new Exception(NOTEMPLETEFOUND);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}

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