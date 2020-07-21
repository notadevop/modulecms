<?php


/**
 *
 */
class ViewRender {

	function __construct() { }


	// Получаем данные из базы данных, какой шаблон используется в данный момент, 

	function getCurTplParams() {

		require_once $this->tplDir . $this->activateTpl . 'schema.tpl.php';
		
		return layoutScheme();
	}

	// Список всех шаблонов и выбраных шаблонов

	private $activateTpl 	= TPLDEFAULTTEMPLATE;
	private $tplDir 		= TPLDEFAULTFOLDER;

	function setActiveTemplate(string $template=''): void {

		// TODO: Проверить существует ли данный шаблон или нет!

		$this->activateTpl = (!empty($template)) ? $template . DS : $this->activateTpl;
	}

	function getAListOfTemplates(): array {

		return array();
	}

	// sidebar = array('sidebar.tpl.php', $params), posts

	function activateTemplate(array $templates): bool{

		// Используется если хотите установить активный шаблон
		
		return false;
	}

	// Генерируем превью для шаблонов

	// TODO: 123 <== Временно, написать класс ViewRender.class.php => pageBuilder.ctrl.php

	function genPrevMap($routes, $result, $curRoute):void {

		// TODO: получаем созданый дизайнере xml файл где раставленны как и какой шаблон должны идти
		// тут указываем, что показывать и засовываем данные

		$regOk = false;

		if (defined('PROFILE') && !empty(PROFILE['useremail'])) {

			$regOk = true; 
		}

		if ( !$curRoute || empty($routes[$curRoute['uri']]) ) {

			$defTpl 	= $routes['/404page']['template'];
			$ifRegOk 	= $routes['/404page']['ifRegOk'];
		} else {

			$defTpl 	= $routes[$curRoute['uri']]['template'];  
			$ifRegOk 	= $routes[$curRoute['uri']]['ifRegOk'];
		}

		$renderTpl = ($regOk) ? $ifRegOk : $defTpl;

		// TODO: перенести в класс рендеринга и там загружать настройки шаблона и по нему выводить страницы 

		// Временно !!!!!

		$tplFolder = $this->tplDir . $this->activateTpl;

		foreach ($this->getCurTplParams() as $value) {

			if ($value == 'content') {

				require_once $tplFolder . $renderTpl;
			} else {

				require_once $tplFolder . $value . '.tpl.php';
			}
		}
	}

	// Выводим html шаблоны в просмотр интерфейс.

	function renderView(string $templateName): void {

		require_once $templateName;
	}
}
