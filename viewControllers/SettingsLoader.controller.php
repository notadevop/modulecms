<?php
namespace Settings;

class SettingsController {

	function settingsFromDatabase() {

	}

	private $settings;
	private $settingsFolder = 'Includes';

	function loadSettings() {

		$settingsFileIndexes = Array(
			'settings.inc.php'
		);

		$settings = array();

		$path = ROOTPATH.$this->settingsFolder;

		try {
			foreach ($settingsFileIndexes as $settFile) {

				$file = $path.DS.$settFile;

				if(!file_exists($file)) {
					throw new Exception('Could not found a settings file!');
				} else {
					$this->settings[] = require_once ($file);
				}
			}

		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	function takeSettings() {

		return $this->settings;
	}

}
