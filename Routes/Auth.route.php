<?php 


$routes = array(

	'/login' 		=> array( 
			'action' 	=> 'UserIdentificator/loginAction',
			'template'	=> 'login.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Логин',
			'priority'	=> 4
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
			'template'	=> 'updatepass.tpl.php',
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
);