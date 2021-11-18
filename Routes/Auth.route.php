<?php 

(defined('ROOTPATH') && defined('DS')) || die('something wrong');

return array(

	// Вход в систему

	'/login' => array( 
		'url' 		=> '/login', 
		'action' 	=> 'Identificator/loginAction',
		'template'	=> 'authforms/login.tpl.php',
		'priority'	=> 4,
		'rendertype'=> 'authui',
	),

	// Авторизация через куки

	'auth' 			=> array(
		'url' 		=> 'auth', 	
		'action' 	=> 'Identificator/authAction',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 1,
		'rendertype'=> null,
	),

	// Регистрация пользователя

	'/register' 	=> array(
		'url' 		=> '/register', 
		'action' 	=> 'Identificator/registrationAction',
		'template'	=> 'authforms/registration.tpl.php',
		'priority'	=> 4,
		'rendertype'=> 'authui',
	),

	// Восстановление пароля

	'/restore' 		=> array(
		'url' 		=> '/restore', 
		'action' 	=> 'Identificator/restoreAction',
		'template'	=> 'authforms/restore.tpl.php',
		'priority'	=> 4,
		'rendertype'=> 'authui',
	),

	// Форма для введения нового пароля для пользователя

	'/verifyrestorerequest' => array(
		'url' 		=> '/verifyrestorerequest', 
		'action' 	=> 'Identificator/verifyUserActivation',
		'template'	=> 'authforms/passwords.tpl.php',
		'priority'	=> 2,
		'rendertype'=> 'authui',
	),

	// Обработчик данных (новых паролей) для пользователя

	'/updatepassword' 	=> array(
		'url' 		=> '/updatepassword', 
		'action' 	=> 'Identificator/updateUserPassword',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 2,
		'rendertype'=> 'authui',
	),

	// Страница активации зарегестрированного пользователя

	'/verifreg' 	=> array(
		'url' 		=> '/verifreg',
		'action' 	=> 'Identificator/verifyUserRegistration',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 2,
		'rendertype'=> 'authui',
	),

	// Выход из системы

	// Инициализировать CSRF проверку, чтобы не могли выйти удаленно 
	// /logout/user_token_as_:any attack protection 

	'/logout' 		=> array(
		'url' 		=> '/logout',
		'action' 	=> 'Identificator/logout/true/false',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
		'rendertype'=> 'authui',
	),
);