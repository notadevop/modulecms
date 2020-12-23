<?php 

(defined('ROOTPATH') && defined('DS')) or die('something wrong!');


class HostSettings extends Database {


	function __construct() { 

		parent::__construct(true);
	}

	public function settingExist(string $option_key): bool {

		$sql = 'SELECT COUNT(*) FROM website_options WHERE option_name LIKE :optname LIMIT 1';

		$this->preAction($sql, array(':optname' => $value));

		if (!$this->doAction()) {return false;}

		$count = $this->postAction()->fetchAll(PDO::FETCH_ASSOC);

		if (count($count['count']) >= 1) { return true; }
		
		return false;
	}

	// Получаем все указанные настройки по указанному массиву 

	public function getSettings(array $option_key): ?array{

		$sql = 'SELECT option_value as value 
				FROM website_options WHERE option_name LIKE :optname LIMIT 1';

        $row = array();

        foreach ($option_key as $key => $value) {

        	if (!$this->settingExist($key)) { continue; }

        	$this->preAction($sql, array(':optname' => $value));
 
        	if(!$this->doAction()) { continue; }

        	$row[$value] = $this
        						->postAction()
        						->fetchAll(PDO::FETCH_ASSOC); // <---- ????
        }

        $row = array_filter($row);

        return !empty($row) ? $row : null;
	}

	// Добавляем новые настройки или обновляем при включенном флаге старые

	function addSettings(string $settings): bool { 

		if (empty($settings) || $this->settingsExist($key)) { return false; }

		$sql = 'INSERT INTO website_options (option_name, option_value) 
						VALUES (:optname, optvalue)';

		$this->preAction($sql, array(
									':optname' 	=>	$key), 
									':optvalue'	=>	$value;
									);
		if(!$this->doAction()) { return false; }

		return true;
	}

	// редактирует указанный список настроек для хоста

	function editSettings(string $settings): bool {

		if 

	}

	function removeSettings(string $option_key): bool {


	}

}