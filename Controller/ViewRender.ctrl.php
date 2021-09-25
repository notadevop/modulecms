<?php

(defined('ROOTPATH') && defined('DS')) or die('something wrong!');

/**
 *
 */
class ViewRender {

	// Список всех шаблонов и выбраных шаблонов

	private $currentTpl;
	private $tplDir;
	private $htmlRenderRes;
	private $curRouteName;

	function __construct() { 

		//$this->curRouteName 	= Router::getCurrentRouteParams();
		$this->curRouteName 	= Router::getRoute();
		$this->replaceParams 	= array();

		$this->currentTpl 		= TPLDEFTEMPLATE;
		$this->tplDir 			= TPLDEFAULTFOLDER;
		$this->htmlRenderRes	= NULL;
	}

	private function AdminZone(): bool { return false; }

	// Получаем данные из базы данных, какой шаблон используется в данный момент, 

	// Удалить поже, схема не нужна --- 

	function getTemplateSchema() {

		$dir	= $this->tplDir;
		$tpl 	= $this->currentTpl;

		return require_once $dir.$tpl.'schema.tpl.php';
	}

	private $activeTemplate;

	function setActiveTemplate(string $template=''): void {

		$tmpTplName = null;

		/*
		if (!empty($template) && file_exists(filename)) {

			$tmpTplName = 
		}
		*/

		// TODO: Проверить существует ли данный шаблон или нет!

		// Загружаем активную версию шаблона при условии, если мы не в административной части !!!!!

		// Загружаем с таблицы website_options -> website_active_template -> simplelight


		$this->currentTpl = !empty($template) ? $template . DS : $this->currentTpl;
	}

	private $hostSettings;

	function initHostSettings(array $pararms): ?array{

		$hostParams = array(

			'activeTpl' 	=> null,
			'websiteTitle'	=> 'Not set yet',
			'websiteDescrip'=> 'Not set yet',
			'username'		=> PROFILE['username'],

		);

		// Устанавливаем тему по умолчанию 
		// устанавливаем имя зарегестрированного пользователя
		// время загрузки

		// Получаем нужные настройки из базы

		return null;
	}

	// Нужно отдельный шаблон и пути для АДМИНИСТРАТИВНОЙ ЧАСТИ !!!!!!!!!

	function prepareRender($result) {

		// TODO: получаем созданый дизайнере xml файл где раставленны как и какой шаблон должны идти
		// тут указываем, что показывать и засовываем данные

		$regOk = false;

		if (defined('PROFILE') && !empty(PROFILE['useremail'])) {

			$regOk = true; 
		}

		$routes = Router::getRoute(true);

		if ( !$this->curRouteName || empty($this->curRouteName) ) {

			$defTpl 	= $routes['/404page']['template'];
			$ifRegOk 	= $routes['/404page']['ifRegOk'];

		} else {

			$defTpl 	= $routes[$this->curRouteName['uri']]['template'];  
			$ifRegOk 	= $routes[$this->curRouteName['uri']]['ifRegOk'];
		}

		$renderTpl = ($regOk) ? $ifRegOk : $defTpl;
		$tplFolder = $this->tplDir . $this->currentTpl;

		ob_start();

		foreach ($this->getTemplateSchema() as $value) {

			// Если из схемы выходит content то заменяем его шаблоном из route - url

			require_once ($tplFolder . ($value == 'content' ? $renderTpl : $value . '.tpl.php') );
		}

		$this->htmlRenderRes = ob_get_contents();
		ob_end_clean();
	}

	private $replaceParams;

	function replace(array $params): void {

		if(empty($params)) return ;
		
		$html = $this->htmlRenderRes;

		foreach ($params as $key => $value) {
			
			$html = preg_replace($key, $value, $html);
		}

		$this->htmlRenderRes = $html;
	}
	
	
	function viewRender(): void {

		// Использовать preg_replace то, что нужно заменить 
		print($this->htmlRenderRes);
	}

	function __desctructor(){}
}
