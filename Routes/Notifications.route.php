<?php 
/**
 *
 *  Путь по уведомлениям
 */

return array(

	// Профиль пользователя который хочет посмотреть свой аккаунт

	'notifications' => array(
		'url' 		=> '',
		'urltitle'  => '', 
		'action' 	=> 'NotificationController/countNotifications',
		'template'	=> '',
		'priority'	=> 3,
	),

	'/admin/notifications' => array(
		'url' 		=> '/admin/notifications', 
		'urltitle'  => NOTIFSPAGE, 
		'url' 		=> '/admin/notifications',
		'action' 	=> 'NotificationController/getAllNotifications',
		'template'	=> 'NotificationsList.tpl.php',
		'priority'	=> 4,
	),
);