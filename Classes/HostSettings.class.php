<?php 

(defined('ROOTPATH') && defined('DS')) or die('something wrong!');


class HostSettings extends Database {


	function __construct() { 

		parent::__construct(true);
	}

	// Получаем все указанные настройки по указанному массиву 

	public function getSettings(array $option_key): ?array{

		$sql = 'SELECT option_value as value 
				FROM website_options WHERE option_name LIKE :name LIMIT 1';

        $row = array();

        foreach ($option_key as $key => $value) {

        	$this->preAction($sql, array(':name' => $value));
 
        	if(!$this->doAction()) {
        		continue;
        	}

        	$row[$value] = $this
        					->postAction()
        					->fetchAll(PDO::FETCH_ASSOC);
        }

        $row = array_filter($row);

        return !empty($row) ? $row : null;
	}

	function addSettings(array $settings): bool {



	}

	function editSettings(): bool {

	}

	function removeSettings(): bool {


	}

}