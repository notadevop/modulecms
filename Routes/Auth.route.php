<?php 


return array(

	'/login' 		=> array( 
			'action' 	=> 'Identificator/loginAction',
			'template'	=> 'login.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Логин',
			'priority'	=> 4
	),

	'auth' 			=> array(
			'action' 	=> 'Identificator/authAction',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> true, // Permanent or not if yes, !!!
			'decript'	=> 'Аутентификация',
			'priority'	=> 1
	),

	// Регистрация пользователя

	'/register' 	=> array(
			'action' 	=> 'Identificator/registrationAction',
			'template'	=> 'register.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Регистрация',
			'priority'	=> 4
	),

	// Восстановление пароля

	'/restore' 		=> array(
			'action' 	=> 'Identificator/restoreAction',
			'template'	=> 'restore.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Восстановить аккаунт',
			'priority'	=> 4
	),
	'/verifyrestorerequest' => array(
			'action' 	=> 'Identificator/verifyUserActivation',
			'template'	=> 'updatepass.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Проверить восстановление',
			'priority'	=> 2
	),
	'/updatepassword' 	=> array(
			'action' 	=> 'Identificator/updateUserPassword',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Обновление пароля',
			'priority'	=> 2
	),
	'/verifreg' 	=> array(
			'action' 	=> 'Identificator/verifyUserRegistration',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Подтверждение регистрации',
			'priority'	=> 2
	),

	// /logout/user_token_as_:any attack protection

	'/logout' 		=> array(
			'action' 	=> 'Identificator/logout/true/false',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Выйти',
			'priority'	=> 3
	),
);