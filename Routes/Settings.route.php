<?php 

/**
 *
 * 
 */


return array(

	// Профиль пользователя который хочет посмотреть свой аккаунт

	'/admin/settings/website' => array(
		'url' 		=> '/admin/settings/website',
		'urltitle' 	=> SETTINGSHOST,
		'action' 	=> 'SettingsController/updateSettings',
		'template'	=> 'websiteSettings.tpl.php',
		'priority'	=> 3,
	),
);