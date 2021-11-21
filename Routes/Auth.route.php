<?php 

defined('ROOTPATH') || die('something wrong');

return array(

	// Вход в систему

	'/login' => array( 
		'url' 		=> '/login/login',
		'urltitle'  => LOGINPG,
		'action' 	=> 'Identificator/loginAction',
		'template'	=> 'authforms/login.tpl.php',
		'priority'	=> 4,
	),

	// Авторизация через куки

	'auth' 			=> array(
		'url' 		=> 'auth', 	
		'urltitle'  => '',
		'action' 	=> 'Identificator/authAction',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 1,
	),

	// Регистрация пользователя

	'/register' 	=> array(
		'url' 		=> '/login/register', 
		'urltitle'  => REGISTERPAGE,
		'action' 	=> 'Identificator/registrationAction',
		'template'	=> 'authforms/registration.tpl.php',
		'priority'	=> 4,
	),

	// Восстановление пароля

	'/restore' 		=> array(
		'url' 		=> '/login/restore', 
		'urltitle'  => RESTOREPAGE,
		'action' 	=> 'Identificator/restoreAction',
		'template'	=> 'authforms/restore.tpl.php',
		'priority'	=> 4,
	),

	// Форма для введения нового пароля для пользователя

	'/verifyrestorerequest' => array(
		'url' 		=> '/login/verifyrestorerequest',
		'urltitle'  => VERIFYRESTORE,
		'action' 	=> 'Identificator/verifyUserActivation',
		'template'	=> 'authforms/passwords.tpl.php',
		'priority'	=> 2,
	),

	// Обработчик данных (новых паролей) для пользователя

	'/updatepassword' 	=> array(
		'url' 		=> '/login/updatepassword', 
		'urltitle'  => PWDUPDATE,
		'action' 	=> 'Identificator/updateUserPassword',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 2,
	),

	// Страница активации зарегестрированного пользователя

	'/verifreg' 	=> array(
		'url' 		=> '/login/verifreg',
		'urltitle'  => VERIFYREGIST,
		'action' 	=> 'Identificator/verifyUserRegistration',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 2,
	),

	// Выход из системы

	// Инициализировать CSRF проверку, чтобы не могли выйти удаленно 
	// /logout/user_token_as_:any attack protection 

	'/logout' 		=> array(
		'url' 		=> '/login/logout',
		'urltitle'  => LOGOUTPAGE,
		'action' 	=> 'Identificator/logout/true/false',
		'template'	=> 'infopage.tpl.php',
		'priority'	=> 3,
	),
);