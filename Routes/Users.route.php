<?php

// файл путей пользователя

$routes = array(

	'/profile' 		=> array(
			'action' 	=> 'ProfileController/getUserProfile',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'profile.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),

);