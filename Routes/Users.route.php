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
		'action' 	=> 'ProfileController/getAllUsers',
		'template'	=> 'listUsers.tpl.php',
		'priority'	=> 3,
		'rendertype'=> 'adminui'
	),

	// Страница показывает всех пользователей онлайн

	'/usersonline' 	=> array(
		'url' 		=> '/usersonline',
		'action' 	=> 'Visitor/getOnlineUsers',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
		'rendertype'=> 'adminui'
	),

	// Отрабатывает для показа счетчика пользователей онлайн и регистрирует новых

	'online' => array(
		'url' 		=> 'online',
		'action' 	=> 'Visitor/users_online',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 4,
		'rendertype'=> null
	),

);