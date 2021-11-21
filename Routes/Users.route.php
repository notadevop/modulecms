<?php

// файл путей пользователя

/*
	/profile -> используетcя как определение, аккаунта пользователя 
	/profile/:num -> используется для показа указанного пользователя или редактирования (для администратора)
	/profile/edit/:num -> используется для редактирования

	/profile/remove/:num
	
	NB! Удаление, сперва блокировка на 1-2 недели, а потом удаление аккаунта как постоянно

	/listusers
*/


return array(

	// Профиль пользователя который хочет посмотреть свой аккаунт

	'/admin/users' => array(
		'url' 		=> '/admin/users',
		'urltitle'  => USERSLISTPAGE,
		'action' 	=> 'ProfileController/getAllUsers',
		'template'	=> 'listUsers.tpl.php',
		'priority'	=> 3,
	),

	// Страница показывает всех пользователей онлайн

	'/usersonline' 	=> array(
		'url' 		=> '/usersonline',
		'urltitle'  => USERSONLINEPG,
		'action' 	=> 'Visitor/getOnlineUsers',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
	),

	// Отрабатывает для показа счетчика пользователей онлайн и регистрирует новых

	'online' => array(
		'url' 		=> 'online',
		'urltitle'  => '',
		'action' 	=> 'Visitor/users_online',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 4,
	),
);