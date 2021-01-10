<?php 

(defined('ROOTPATH') && defined('DS')) or die('something wrong!');

class ViewRender {

	/*
	private $defaultTpl;
	private $defaultTplDir;
	private $currentActiveTpl;
	*/

	private $templateSettings; // array

	private $hostSettings; // array

	private $viewContent; // string -> content

	private $reservedPages; 


	function __constructor() {

		$this->hostSettings = array(

				'/%websiteTitle%/i' 	=> 'Заголовок вебсайта',
				'/%websiteDesc%/i'		=> 'Пояснение вебсайта',
				'/%username%/i'			=> PROFILE['username']
				'/%CurActiveTemplate%/i'=> TPLDEFTEMPLATE
		);
	}

	// Получаем все настройки 

	function initRenderSettings():void {

		$hostTmp = $this->hostSettings;

		$tmp = new hostSettings()->getSettings($hostTmp);

		$hostTmp= array_replace(array_intersect_key($hostTmp, $tmp), $tmp);

		$this->hostSettings = $hostTmp;
	}

	function initSpecPages(array $listSpecPages): ?string {

		$pages = array(
					// Административная часть
					'admin' => 
						array(	
							'tplName'		=> 'adminDefault',
							'folderName' 	=> '/adminTpl',
							'link'	 		=> '/administrator'
					 	)  	
				);

		$pages = $listSpecPages

		// сделать механизм выбора специальных шаблонов для определенных страниц 
		// Например для адимнистратора указывается свой шаблон

		return null;
	}


	function activateTemplate(string $tplName=''):void {

		// -> TPLDEFAULTFOLDER <- 



	}

	// Заменяет маску ключа и его значением => %mask% - %replacement%

	function replaceContent(array $params):void{

		if (empty($params)) { return; }

		$this->viewContent = preg_replace(
									array_keys($params), 
									array_values($params), $this->viewContent
							);
	}

	function prepareRender():void {

		// Сканируем все файлы *.tpl.php в указаной папке с шаблона



		$this->replaceContent($this->hostSettings);
	}

	function viewRender():void {


		if(!HOSTENABLED) {

			die('Sorry, host temporary closed/Веб хост временно закрыт!');
		} 

		echo $this->viewContent;
	}
}