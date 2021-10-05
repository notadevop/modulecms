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

		$this->regOk = false;

		if(defined('PROFILE') && !empty(PROFILE['useremail'])) {

			$this->regOk = true;
		}

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
		$this->activeTpl = $folder.$name.DS;
		return $tplarr;
	}

	// Метод определяет какой тип шаблона нужно вывести 
	// например админка, окно авторизации или стандартный 

	function initTypeOfRender(): array {

	}

	function prepareRender() {

		$currentRoute 	= $this->currentRoute;
		$Allroutes  	= Router::getRoute(true);

		if(empty($currentRoute)) {
			$defTpl = $Allroutes['/404page']['template'];
		}else{
			$defTpl = $Allroutes[$this->currentRoute['uri']]['template'];
		}

		$r = $this->activateTemplate($this->params['website_template']);
		//$r = $this->activateTemplate('admin');

		if (!$r) {
			$r = $this->activateTemplate(TPLDEFTEMPLATE);
		}

		if(!$r) {
			die('No Render! Template Not Found!');
		} else if (!file_exists($this->activeTpl.$defTpl)) {
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

	/*
	function prepareRender() {

		$current = $this->currentRoute;
		$routes  = Router::getRoute(true);

		// Тут определить какой тип страницы открыт. 
		// Админка, окно входа или пользовательский интерфейс

		$r = $this->activateTemplate($this->params['website_template']);

		if (!$r) {
			$r = $this->activateTemplate(TPLDEFTEMPLATE);
		}

		if(!$r) {
			die('No Render! Template Not Found!');
		}

		if (empty($current)) {
			$defTpl 	= $routes['/404page']['template'];
			$ifRegOk 	= $routes['/404page']['ifRegOk'];
		} else {
			$defTpl 	= $routes[$this->currentRoute['uri']]['template'];
			$ifRegOk 	= $routes[$this->currentRoute['uri']]['ifRegOk'];
		}

		$renderTpl = ($this->regOk) ? $ifRegOk : $defTpl;

		// TODO: Тут определить тип языка и по нему вывести языковый пакет
			
		ob_start();
		foreach ($r['templates'] as $value) {
			// Если из схемы выходит content то заменяем его шаблоном из route - url
			$tmp = $value == 'content' ? $renderTpl : $value; 
			if (file_exists($this->activeTpl.$tmp))
				require_once ($this->activeTpl.$tmp);
			else {
				echo 'template not found<br />';
				echo $this->activeTpl.$tmp;
			} 
		}

		$this->htmlRenderRes = ob_get_contents();
		ob_end_clean();

		$replaceParams = array(
			' %title% ' 			=> 'Модульная CMS',
			' %sitetitle% ' 			=> $this->params['website_title'],
			' %site_description% ' 	=> $this->params['website_title_description'],
		);

		$this->replace($replaceParams);
	}
	*/

	function replace(array $params): void {
		if(empty($params)) return;
		$html = $this->htmlRenderRes;
		foreach ($params as $key => $value) {
			//$html = preg_replace($key, $value, $html);
			$html = str_replace($key, $value, $html);
		}
		$this->htmlRenderRes = $html;
	}

	function viewRender(): void {

		//Set the header to utf-8 for example purposes.
		header('Content-Type: text/html; charset=utf-8');
		// Использовать preg_replace то, что нужно заменить 
		print($this->htmlRenderRes);
	}

	function __desctructor(){}
}