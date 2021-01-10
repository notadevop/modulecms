<?php 


(defined('ROOTPATH') && defined('DS')) || die('something wrong');


return array(

	// Страницы без информации -----------

	'/profile' => array(
			'action' 	=> 'ProfileController/getUserProfile',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),

	'/profile/edit' => array(
			'action' 	=> 'ProfileController/editUserProfile',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),

	'/profile/remove' => array(
			'action' 	=> 'ProfileController/editUserProfile',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),

	// Вывод профиля пользователя

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

	// Удаление указанного пользователя 

	'/profile/remove/:any' => array(
			'action' 	=> 'ProfileController/getUserProfile',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'editProfile.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),	

);