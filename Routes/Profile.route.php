<?php 


(defined('ROOTPATH') && defined('DS')) || die('something wrong');


return array(

	// Вывод указанного пользователя
	
	'/admin/profile/:num' => array(
		'url' 		=> '/admin/profile/:num',
		'action' 	=> 'ProfileController/getUserProfile/$1',
		'template'	=> 'viewprofile.tpl.php',
		'priority'	=> 3,
		'rendertype'=> 'adminui',
	),

	// Редактирование пользователя 

	'/admin/profile/edit/:num' => array(
		'url' 		=> '/admin/profile/edit/:num',
		'action' 	=> 'ProfileController/editUserProfile/$1',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
		'rendertype'=> 'adminui',
	),

	// Удаление пользователя 

	'/admin/profile/remove' => array(
		'url' 		=> '/admin/profile/remove',
		'action' 	=> 'ProfileController/getUserProfile',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
		'rendertype'=> 'adminui',
	),	

	// Заглушка для всех других ссылок

	'/admin/profile/:any' => array(
		'url' 		=> '/admin/profile/:any',
		'action' 	=> 'ProfileController/getUserProfile',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
		'rendertype'=> 'adminui',
	),
);