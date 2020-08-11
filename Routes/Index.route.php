<?php



$routes = array(

	// url путь 
			// Контроллер и его метод
			// шаблон для отображения
			// выводить шаблон, если пользователь зарегестрирован
			// Запускать постоянно, вне зависимости от пути , если включен true, то шаблон не используем

			// Некоторые ссылки нужно показывать только зарегестрированным пользователям, некоторые не показывать вообще, например ccылка для подтверждения регистрации, и подтвержд. активации.

			/* приоритеты 
					1. запускать в независимости от uri,
					2. внутриннии ссылки или служебные ссылки
					3. Показывать только зарегестрированным пользователям
					4. Показывать всем пользователям
					5. Не показывать вообще эти ссылки и использовать по умолчанию страницу
			*/
	
	'/' 			=> array( 
			'action' 	=> 'MainController/defaultMethod',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Главная',  // TODO: ВРЕМЕННО, для указания ссылки, будет использоваться отдельно, в языковом пакете
			'priority'	=> 4 // Показывать по при оритету
	),
	'online' 		=> array(
			'action' 	=> 'Visitor/users_online',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> true, // Permanent or not if yes, !!!
			'decript'	=> 'Список пользователей онлайн',
			'priority'	=> 4
	),
	'/usersonline' 	=> array(
			'action' 	=> 'Visitor/getOnlineUsers',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Пользователи Онлайн',
			'priority'	=> 3
	),
	'/404page' 		=> array(
			'action' 	=> 'MainController/defaultMethod',
			'template'	=> '404page.tpl.php',
			'ifRegOk'	=> '404page.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Страница 404',
			'priority'	=> 2
	),
);