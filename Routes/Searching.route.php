<?php 

(defined('ROOTPATH') && defined('DS')) || die('something wrong');


return array(

	// Поиск по вебсайту у пользователя
	
	'/search' => array(
		'url' 		=> '/search',
		'urltitle'  => 'Поиск по сайту', 
		'action' 	=> 'Explorer/getSearch',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
	),

	// Поиск по вебсайту у администратора

	'/admin/search' => array(
		'url' 		=> '/admin/search',
		'urltitle'  => 'Поиск в админ части', 
		'action' 	=> 'Explorer/getAdminSearch',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
	),
);