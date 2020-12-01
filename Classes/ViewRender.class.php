<?php
/**
 *
 */
class ViewRender {

	function __construct(array $currentRouteName) { 

		$this->curRouteName 	= $currentRouteName;
		$this->replaceParams 	= array();
	}


	// Получаем данные из базы данных, какой шаблон используется в данный момент, 

	function getTemplateSchema() {

		return require_once $this->tplDir.$this->currentTpl.'schema.tpl.php';
		
		//return layoutScheme();
	}

	// Список всех шаблонов и выбраных шаблонов

	private $currentTpl 	= TPLDEFAULTTEMPLATE;
	private $tplDir 		= TPLDEFAULTFOLDER;
	private $htmlRenderRes	= NULL;

	private $curRouteName 	= false;

	function setActiveTemplate(string $template=''): void {

		// TODO: Проверить существует ли данный шаблон или нет!

		$this->currentTpl = (!empty($template)) ? $template . DS : $this->currentTpl;
	}


	function prepareRender($routes, $result, $curRoutePath=false): void {

		// TODO: получаем созданый дизайнере xml файл где раставленны как и какой шаблон должны идти
		// тут указываем, что показывать и засовываем данные

		$regOk = false;

		if (defined('PROFILE') && !empty(PROFILE['useremail'])) {

			$regOk = true; 
		}

		if ( !$this->curRouteName || empty($routes[$this->curRouteName['uri']]) ) {

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

}
