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

	private $reservedPages;


	function __construct() {

		// Как то определить к какому пути относится какой шаблон

		Router::initDefaultRoutes();

		$this->reservedPages = array(

			'adminpage'   	=> Router::modifyRoutes('admin'),
			'loginpage'  	=> Router::modifyRoutes('login'),
		);

		// Результат от постоянных исполнителей 
		// и от того, что идет по определенному пути  
		//$this->permanRes 		= Router::getPermanentResult();
		//$this->currenRes 		= Router::getResult();

		// Тут нужно разделить исполнение постоянное и то, что запускается по пути шаблона

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

		// Устанавливает папку где лежат шаблоны, по умолчанию либо через пользователя
		$folder = !empty($folder) ? ROOTPATH.$folder.DS : $this->currentTplDir;
		
		// Проверяем есть ли схема у нее
		$fpath = $folder.$name.DS.TPLSCHEMEFILE;
		
		if (!file_exists($fpath)) { return null; }
		
		$tplarr = require_once($fpath);
		if(empty($tplarr)) { return null; }
		$this->activeTpl 					= $folder.$name.DS;
		$this->params['website_template'] 	= $name;
		return $tplarr;
	}

	function prepareRender() {

		$ui = null;

		// Разделяем URI для определения какая страница перед нами

		if(!empty(isset($this->currentRoute['uriarr'][0]))){

			$ui = $this->currentRoute['uriarr'][0];
		}


		//vardump($ui);
		//vardump($this->reservedPages['loginpage']);

		// Определяет нужно заблокировать вывод или на оборот
		// нужно например для административной части вывода вместо html json 

		$deniedTpl = false;

		if($ui == $this->reservedPages['adminpage'] && $this->regOk) {
			$r = $this->activateTemplate('admin');
		}elseif ($ui == $this->reservedPages['loginpage']) {
			$r = $this->activateTemplate('login');
		} else {
			if($ui == $this->reservedPages['adminpage'] && !$this->regOk) {
				$deniedTpl = true;
				$r = $this->activateTemplate('login');
			} else {
				$r = $this->activateTemplate($this->params['website_template']);
			}

			// Тут нужно указать шаблон логина 
			//$r = $this->activateTemplate($this->params['website_template']);
			
		}

		// Определяет какую страницу нужно вывести 

		if(empty($this->currentRoute)) {
			$defTpl = $this->allRoutes['/404page']['template'];
		}elseif ($deniedTpl){
			$defTpl = $this->allRoutes['/login']['template'];
		//}elseif ($hostDisabled && !LOGIN && !ADMIN) {
		//	$defTpl = $this->allRoutes['/disabled']['template'];
		} else { 
			$defTpl = $this->allRoutes[$this->currentRoute['url']]['template'];
		}

		// Тут устанавливается cистемный или резервный шаблон  
		// если в системе не оказалось шаблона
		if (!$r) {
			$r = $this->activateTemplate(TPLTEMPLATE);
		}

		ob_start();

		try {

			if(!$r || !file_exists($this->activeTpl.$defTpl)) {

				throw new Exception(NOTEMPLETEFOUND.' -> '.$this->activeTpl.$defTpl);
			}

			if(isset($r['languagePack'][DEFLANGUAGE])) {
				
				if (file_exists($this->activeTpl.$r['languagePack'][DEFLANGUAGE])) {
					require_once($this->activeTpl.$r['languagePack'][DEFLANGUAGE]);
				} else {
					Logger::collectAlert(Logger::WARNING, NOLANGUAGEPACK);
				}
			}

			if (!require_once ($this->activeTpl.$defTpl)) {
				throw new Exception('tpl require error x.x');
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		$this->htmlRenderRes = ob_get_contents();
		ob_end_clean();

		$replaceParams = array(
			' %title% ' 			=> $this->params['website_title_description'],
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
		header('X-Powered-By: ModuleCMS');
		
		print($this->htmlRenderRes);
	}

	function __desctructor(){}
}