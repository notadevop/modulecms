<?php 

(defined('ROOTPATH') && defined('DS')) or die('something wrong!');


class HostSettings extends Database {


	function __construct() { 

		parent::__construct(true);
	}

	// Возвращает массив имени и его значения!

	private function getSettings(string $name): ?array{

		$sql = 'SELECT option_name as name, option_value as value 
				FROM website_options WHERE option_name LIKE :name LIMIT 1';

        $this->preAction($sql);

        $this->binder(array(':name' => $name));

        if(!$this->doAction()) {
        	return null;
        }

        $row = $this
        		->postAction()
        		->fetchAll(PDO::FETCH_ASSOC);

        return !empty($row) ? $row : null;
	}

	function addSettings(): bool {

	}

	function editSettings(): bool {

	}

	function removeSettings(): bool {


	}

}