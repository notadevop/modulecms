<?php 
/**
 *
 *  Путь по уведомлениям
 */

return array(

	// Профиль пользователя который хочет посмотреть свой аккаунт

	'notifications' => array(
			'action' 	=> 'Notifications/geAll',
			'template'	=> '',
			'priority'	=> 3,
			'rendertype'=> 'userui'
	),

	'/admin/notifications' => array(
			'action' 	=> '',
			'template'	=> '',
			'priority'	=> 4,
			'rendertype'=> 'adminui'
	),
);