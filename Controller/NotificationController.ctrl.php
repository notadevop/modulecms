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

	// Получаем количество всех уведомлений 

	function getAllNotifications(): ?string {


		return null;
	}


}