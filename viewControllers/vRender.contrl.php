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

	function __construct() {

		// Как то определить к какому пути относится какой шаблон

		Router::initDefaultRoutes();
		$this->result = Router::getResult();
		$this->currentRoute = Router::getRoute();

		$this->regOk = false;

		// pages это массив для определения шаблона для определенных страниц 
		// например чтобы разные страницы имели разные шаблоны

		$pages = array(
			'user' => null,
			'admin'=> null,
			'auth' => null
		);

		$this->currentTplDir = TPLDEFAULTFOLDER;

		$settings = new HostSettings();

		$prm = array(
			'website_template' 	=> '',
			'website_title'  	=> '',
			'website_title_description' => '',
		);

		$this->params = $settings->getSettings($prm);
	}

	function activateTemplate(string $name='', string $folder=''): ?array {

		$folder = !empty($folder) ? ROOTPATH.$folder.DS : $this->currentTplDir;

		$fpath = $folder.$name.DS.'schema.tpl.php';

		if (!file_exists($fpath)) { return null; }

		$tplarr = require_once($fpath);

		if(empty($tplarr)) { return null; }

		$this->activeTpl = $fpath;

		return $tplarr;
	}

	function prepareRender() {

		$tpl = false;

		//if($this->currentRoute == $pages['users']) {
			//$settings = new HostSettings();

			//$tplName = $settings->getSettings(['website_template']);



			$r = $this->activateTemplate($this->params['website_template']);

			if (!$r) 
				echo 'Шаблон не установлен!';
		//}

		// Определяем что перед нами, 
	}
}