<?php 

(defined('ROOTPATH') && defined('DS')) or die('something wrong!');

class ViewRender {


	private $defaultTpl;
	private $defaultTplDir;
	private $currentActiveTpl;

	protected $hostSettings; 

	private $viewContent;

	private $reservedPages;


	function __constructor() {

		$this->hostSettings = array(

				'%websiteTitle%' 	=> 'Заголовок вебсайта',
				'%websiteDesc%'		=> 'Пояснение вебсайта',
				'%username%'		=> 'Имя пользователя'
		);
	}

	function initRenderSettings():void {

		$hostTmp = $this->hostSettings;

		// Получаем сразу все настройки из указанных по умолчанию
		$tmp = new hostSettings()->getSettings($hostTmp);

		$hostTmp= array_replace(array_intersect_key($hostTmp, $tmp), $tmp);

		$this->hostSettings = $hostTmp;
	}

	function initSpecPages(array $listSpecPages): ?string {

		$pages = array(

					'/admin', // Административная 
		);


		// сделать механизм выбора специальных шаблонов для определенных страниц 
		// Например для адимнистратора указывается свой шаблон

		return null;
	}


	function setActiveTemplate(string $tplName='', ):void {

		

	}

	function replaceContent(array $params=''):void{

		if (empty($params)) { return; }

		$html = $this->viewContent;

		foreach ($params as $key => $value) {
			
			$html = preg_replace($key, $value, $html);
		}

		$this->viewContent = $html;
	}

	function prepareRender():void {



		$this->replaceContent($this->hostSettings);
	}

	function viewRender():void {


		if(!HOSTENABLED) {

			die('Sorry, host temporary closed');
		} 

		echo $this->viewContent;
	}
}