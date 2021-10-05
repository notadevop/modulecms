<?php

(defined('ROOTPATH') && defined('DS')) || die('something wrong');

return array(

	/* приоритеты 
	1. запускать в независимости от uri,
	2. внутриннии ссылки или служебные ссылки
	3. Показывать только зарегестрированным пользователям
	4. Показывать всем пользователям
	5. Не показывать вообще эти ссылки и использовать по умолчанию страницу
	*/
	
	// Главная страница или точка входа по умолчанию

	'/' => array( 
			'action' 	=> 'MainController/defaultMethod',
			'template'	=> 'index.tpl.php',
			'priority'	=> 4, // Показывать по при оритету
			'rendertype'=> 'userui',
	),

	// Не найденная страница
	'/404page' 	=> array(
			'action' 	=> 'MainController/defaultMethod',
			'template'	=> 'errors/404page.tpl.php',
			'priority'	=> 2,
			'rendertype'=> 'userui',
	),

	'/admin'  => array(

			'action' 	=> 'MainController/defaultMethod',
			'template'	=> 'dashboard.tpl.php',
			'priority'	=> 4, // Показывать по при оритету
			'rendertype'=> 'adminui',
	),
);