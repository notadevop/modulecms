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


$routes = array(

	// Профиль пользователя который хочет посмотреть свой аккаунт

	'/profile' => array(
			'action' 	=> 'ProfileController/getUserProfile',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'viewProfile.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),

	'/profile/:num' => array(
			'action' 	=> 'ProfileController/getUserProfile/$1',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'viewProfile.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),

	// Редактирование указанного профиля

	'/profile/edit/:num' => array(
			'action' 	=> 'ProfileController/editUserProfile/$1',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'editProfile.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),

	'/profile/edit' => array(
			'action' 	=> 'ProfileController/editUserProfile',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'editProfile.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),

	// Удаление указанного пользователя 

	'/profile/remove/:any' => array(
			'action' 	=> 'ProfileController/getUserProfile',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'editProfile.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),

	'/users' => array(
			'action' 	=> 'ProfileController/getAllUsers',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'listUsers.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),

);