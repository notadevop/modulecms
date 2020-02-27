<?php 

if (!defined('ROOTPATH') || !defined('DS')) {

	header( "refresh:3; url=/" ); 

	// Логируем тут все данные пользователя для отслеживания

	die('Page is empty, This incident will be reported!');
}


// nopath

define('defroutes', array(
		
			// TODO:  как использовать методы которые нужно использовать внезависимости от пути???

			'__hidden_auth__'	=> array(
				'controller' 			=> 'UserIdentificator',
				'action' 				=> 'authAction', 
				'args' 					=> ''
			),

			'__hidden_logout__'	=> array(
				'controller' 			=> 'UserIdentificator',
				'action' 				=> 'logout', 
				'args' 					=> array(true, false)
			),
			
			'auth/login' 			=> array (
				'controller' 			=> 'UserIdentificator',
				'action' 				=> 'runAuth', 
				'args' 					=> 'loginAction'
			),

			'auth/register' 		=> array (
				'controller' 			=> 'UserIdentificator',
				'action' 				=> 'runAuth',
				'args' 					=> 'registrationAction'
			),

			'auth/restore' 			=> array (
				'controller' 			=> 'UserIdentificator',
				'action' 				=> 'runAuth',
				'args' 					=> 'restoreAction'
			),

			'auth/confirmrestore' 	=> array(
				'controller' 			=> 'UserIdentificator',
				'action' 				=> 'runAuth',
				'args' 					=> 'confRestoreAction'
			)
	)
);

