<?php 


(defined('ROOTPATH') && defined('DS')) || die('something wrong');


return array(

	// Вывод указанного пользователя
	
	'/profile/:num' => array(
		'url' 		=> '/admin/profile/:num',
		'urltitle'  => PROFILEVIEW, 
		'action' 	=> 'ProfileController/getUserProfile/$1',
		'template'	=> 'viewprofile.tpl.php',
		'priority'	=> 3,
	),

	// Редактирование пользователя 

	'/profile/edit/:num' => array(
		'url' 		=> '/admin/profile/edit/:num',
		'urltitle' 	=> PROFILEEDIT,
		'action' 	=> 'ProfileController/editUserProfile/$1',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
	),

	// Удаление пользователя 

	'/profile/remove' => array(
		'url' 		=> '/admin/profile/remove',
		'urltitle'  => PROFILEREMOVE,
		'action' 	=> 'ProfileController/getUserProfile',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
	),	

	// Заглушка для всех других ссылок

	'/profile/:any' => array(
		'url' 		=> '/admin/profile/:any',
		'urltitle'  => NOPAGE404,
		'action' 	=> 'ProfileController/getUserProfile',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
	),

	'/profile/' => array(
		'url' 		=> '/admin/profile',
		'urltitle'  => 'Профиль',
		'action' 	=> 'ProfileController/getUserProfile',
		'template'	=> 'viewprofile.tpl.php',
		'priority'	=> 3,
	),

);