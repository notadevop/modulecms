<?php 


(defined('ROOTPATH') && defined('DS')) || die('something wrong');


return array(

	// Вывод указанного пользователя
	
	'/admin/profile/:num' => array(
			'action' 	=> 'ProfileController/getUserProfile/$1',
			'template'	=> 'viewprofile.tpl.php',
			'priority'	=> 3,
			'rendertype'=> 'adminui'
	),

	// Редактирование пользователя 

	'/admin/profile/edit/:num' => array(
			'action' 	=> 'ProfileController/editUserProfile',
			'template'	=> 'infopage.tpl.php',
			'priority'	=> 3,
			'rendertype'=> 'adminui'
	),

	// Удаление пользователя 

	'/admin/profile/remove/:any' => array(
			'action' 	=> 'ProfileController/getUserProfile',
			'template'	=> 'infopage.tpl.php',
			'priority'	=> 3,
			'rendertype'=> 'adminui'
	),	

	// Заглушка для всех других ссылок

	'/admin/profile/:any' => array(
			'action' 	=> 'ProfileController/getUserProfile',
			'template'	=> 'infopage.tpl.php',
			'priority'	=> 3,
			'rendertype'=> 'adminui'
	),
);