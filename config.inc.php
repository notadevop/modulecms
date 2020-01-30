<?php 
/**
*
* Config file
*/

define('SOLT','abcabcabc'); // сгенерирован при установке


define('PRIVATEKEY', 'mycoolprivatekeytohidesomething');
define('PUBLICKEY', 'mymorecoolpublickeytohidesomething');

define('UPDATEAUTHINTERVAL', 7); // Интервал обновления хеша аутентификации 
define('UPDATEPWDINTERVAL', 70); // Интервал обновления пароля пользователя в днях
 

//define('DS', DIRECTORY_SEPARATOR);

define('HOST', 'http://'.$_SERVER['HTTP_HOST']);

// НАСТРОЙКИ: НИЖЕ

// Константа массива для работы с базой данных sqlite 
define('SQLITEJOB', array(
	'sqlitefolder' 	=> 'sqlitefolder',
	'sqlitefile'	=> 'tempdb.sqlite'
));


define('REDIRECTLOGIN', true);

define('REDIRECTORS', array(
	'defPostLogin' 	=> 'profle',
	'defPostReg'	=> '',
	'defPostRest'	=> ''
));

define('REGISTRATIONALLOW', true);
define('LOGINALLOW', true);

/*
	LOGIN 		- откуда человек пришел в пределах сайта
	RESTORE 	- после подтверждения, отправить на главную страницу
	REGISTER 	- после подтверждения регистрации на профиль страницы
	AUTH 		- проверяет скрытно
*/

/*
	Привелегии пользователя могут иметь несколько привелегий .... !!!!

 1. administrator, 		(ALL PRIVELEGES)
 2. moderator/editor  	(CAN EDIT OTHER POSTS, AUTHOR OF POSTS, CAN REMOVE ALL COMMENTS)
 3. author,  			(CAN CREATE HIS POSTS, REMOVE HIS POSTS AND COMMENTS ONLY)
 4. subscriber  		(CAN LEAVE COMMENTS, AND DELETE HIS COMMENTS)
 5. blockeduser			(EXIST IN SYSTEM, NO PRIVELEGES TEMPORARY)
 6. deleteduser 		(EXIST IN SYSTEM NO PRIVELEGES FOREVER)
 7. visitor				(NO PRIVELEGES, CAN VIEW OPEN POSTS ONLY, NOT REGISTERED)
*/

 /*
	Фильтрация данных 

	Легкая 
		Фильтрация от Mysql Injection, XSS. 

	Средняя 
		Фильтрация от Mysql Injection, XSS, Теги конвертирует

	Тяжелая
		Фильтрация от Mysql Injection, XSS, Теги удаляет, и все доступные фильтры которы могут быть
 */


define('ADMINISTRATOR', 1);
define('MODERATOR', 2);
define('AUTHOR', 3);
define('SUBSCRIBER', 4);
define('BLOCKED', 5);
define('DELETED', 6);
define('VISITOR', 7);














