<?php 
/**
 *
 *  Путь по уведомлениям
 */

return array(

	// Профиль пользователя который хочет посмотреть свой аккаунт

	'notifications' => array(
		'url' 		=> 'notifications',
		'action' 	=> 'NotificationController/countNotifications',
		'template'	=> '',
		'priority'	=> 3,
		'rendertype'=> 'userui',
	),

	'/admin/notifications' => array(
		'url' 		=> '/admin/notifications',
		'action' 	=> 'NotificationController/getAllNotifications',
		'template'	=> 'NotificationsList.tpl.php',
		'priority'	=> 4,
		'rendertype'=> 'adminui',
	),
);