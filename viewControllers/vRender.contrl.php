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

	function activeTemplate(string $name='', string $folder=''): bool {

		$folder = !empty($folder) ? ROOTPATH.$folder.DS : $this->currentTplDir;

		$fpath = $folder.$name.'schema.tpl.php';
		if (!file_exists($fpath)) { return false; }
		$this->activeTpl = $fpath;
		return true;	
	}

	function prepareRender() {

		$tpl = false;

		//if($this->currentRoute == $pages['users']) {
			//$settings = new HostSettings();

			//$tplName = $settings->getSettings(['website_template']);

			debugger($this->params);


		//}

		// Определяем что перед нами, 
	}
}