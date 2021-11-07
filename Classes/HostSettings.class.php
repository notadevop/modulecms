<?php 

(defined('ROOTPATH') && defined('DS')) or die('something wrong!');


class HostSettings extends Database {

	function __construct() { 
		parent::__construct(true);
	}

	public function settingsExist(string $key): bool {
		$sql = 'SELECT COUNT(*) as count FROM website_options WHERE option_name LIKE :optname LIMIT 1';
		$this->preAction($sql, array(':optname' => $key));
		if (!$this->doAction()) { return false; }
		$count = $this->postAction()->fetchColumn();
		if ($count > 0) { return true; }
		return false;
	}

	// Получаем все указанные настройки по указанному массиву 

	public function getSettings(array $settings_keys): ?array{

		$sql = 'SELECT option_value as value 
				FROM website_options 
				WHERE option_name = :optname LIMIT 1';

        $row = array();

        foreach ($settings_keys as $key => $value) {
        	if (!$this->settingsExist($key)) { continue; }
        	$this->preAction($sql, array(':optname' => $key));
        	if(!$this->doAction()) { continue; }

        	$settings_keys[$key] = $this->postAction()->fetch()['value'];
        }

        return array_filter($settings_keys);
	}

	// Добавляем новые настройки или обновляем при включенном флаге старые

	protected function addSettings(string $key, string $value): bool { 

		if (empty($key) || $this->settingsExist($key)) { return false; }

		$sql = 'INSERT INTO website_options (option_name, option_value) 
						VALUES (:optname, optvalue)';

		$this->preAction($sql, array(
									':optname' 	=>	$key, 
									':optvalue'	=>	$value
									));
		if(!$this->doAction()) { return false; }

		return true;
	}

	// редактирует указанный список настроек для хоста

	function editSettings(string $key, string $value): bool {

		if(!$this->settingExist($key)) { return false; }

		// Тут не правильно!!???!? так как ориентировка идет на :optname в коде !!!

		$sql = 'UPDATE website_options SET option_name=:optname, option_value=:optvalue WHERE option_name = :optname';

		$this->preAction($sql, array(':optname' => $key, ':optvalue' => $value));

		if (!$this->doAction()) { return false; }

		return true;
	}

	function removeSettings(string $key): bool {

		if(!$this->settingExist($key)) { return false; }

		$sql = 'DELETE FROM website_options WHERE option_name LIKE :optname';
		
		$this->preAction($sql, array(':optname' => $key));

		if(!$this->doAction()) { return false; }

		return true;
	}

}