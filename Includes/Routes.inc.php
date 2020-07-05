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
	'/login' 		=> array( 
			'action' 	=> 'UserIdentificator/loginAction',
			'template'	=> 'login.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Логин',
			'priority'	=> 4
	),
	'/posts/:num' 	=> array(
			'action' 	=> 'MainController/test/$1',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Cтраница постов',
			'priority'	=> 2
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
	'auth' 			=> array(
			'action' 	=> 'UserIdentificator/authAction',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> true, // Permanent or not if yes, !!!
			'decript'	=> 'Аутентификация',
			'priority'	=> 1
	),
	'/register' 	=> array(
			'action' 	=> 'UserIdentificator/regAction',
			'template'	=> 'register.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Регистрация',
			'priority'	=> 4
	),
	'/restore' 		=> array(
			'action' 	=> 'UserIdentificator/resAction',
			'template'	=> 'restore.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Восстановить аккаунт',
			'priority'	=> 4
	),
	'/verifres' 	=> array(
			'action' 	=> 'UserIdentificator/verifyUserModifications',
			'template'	=> 'passform.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Проверить восстановление',
			'priority'	=> 2
	),
	'/confpass' 	=> array(
			'action' 	=> 'UserIdentificator/updatePassword',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Обновление пароля',
			'priority'	=> 2
	),
	'/verifreg' 	=> array(
			'action' 	=> 'UserIdentificator/verifyRegistration',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Подтверждение регистрации',
			'priority'	=> 2
	),
	'/logout' 		=> array(
			'action' 	=> 'UserIdentificator/logout/true/false',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Выйти',
			'priority'	=> 3
	),
	'/profile' 		=> array(
			'action' 	=> 'ProfileController/getUserProfile',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'profile.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Профиль',
			'priority'	=> 3
	),
	'/404page' 		=> array(
			'action' 	=> 'MainController/defaultMethod',
			'template'	=> '404page.tpl.php',
			'ifRegOk'	=> '404page.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Страница 404',
			'priority'	=> 3
	),
);