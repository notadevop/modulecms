<?php 

/**
 * Контроллер уведомленний
 * 
 */

class NotificationController {

	function __construct() { 

		$this->notifs = new Notifications();
	}

	private $notifs;
	private $regOk = true;


	private function getNotifications(bool $getSilence=true, bool $unreadOnly=false): ?array {

		$profile = PROFILE;

		if(empty($profile['userid']) || $profile['userid'] == 0) {

			$this->regOk = false;

			if(!$getSilence) 
				Logger::collectAlert('attentions', 'Профиль не идентифицирован!');

			return null;
		}

		return $this->notifs->getAllNotifs($profile['userid'], $unreadOnly);
	}




	// Получаем количество всех уведомлений 

	function getAllNotifications(): ?array {

		$notifs = $this->getNotifications(false);

		if (!$this->regOk) {
			return null;
		}
	
		if(empty($notifs) || count($notifs) < 1) {

			Logger::collectAlert('information', 'Уведомления отсутвуют!');
			return null;
		} 

		return $notifs;
	}

	// Запускается вне зависимости от пути, выдает количество уведомлений

	function countNotifications(): int {

		if(!($notifs = $this->getNotifications(true, true))) {
			return 0;
		}

		return count($notifs); 
	}
}