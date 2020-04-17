<?php 


/**
 * 
 */
class ViewRender {
	
	function __construct() { 

		$templates = array(
			'header' => $this->tplDir.'header.tpl.php',
			'footer' => $this->tplDir.'footer.tpl.php'
		);
	}

	// Список всех шаблонов и выбраных шаблонов

	private $tplDir = TPLDEFAULTFOLDER.TPLDEFAULTTEMPLATE;

	function setTemplatesFolder(string $folder=''): void {

		$this->tplDir = (!empty($folder)) ? $folder : TPLDEFAULTFOLDER.TPLDEFAULTTEMPLATE;
	}

	// sidebar = array('sidebar.tpl.php', $params), posts

	function addView(array $templates): void {

		foreach ($templates as $key => $value) {
			
			$this->tplDir[$key] = $value;
		}
	}

	// Генерируем превью для шаблонов

	function generatePreviewMap(array $url): void {

		// TODO: получаем созданый дизайнере xml файл где раставленны как и какой шаблон должны идти 


		// тут указываем, что показывать и засовываем данные
	}

	// Выводим html шаблоны в просмотр интерфейс.

	function renderView(): void {

		// максхема как выводить все шаблоны 

	 	require_once ($this->tplDir['header']);
	 	require_once ($this->tplDir['header']);
	 	require_once ($this->tplDir['header']);
	 	require_once ($this->tplDir['footer']);


	}
}