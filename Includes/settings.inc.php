<?php 

defined('ROOTPATH') or die('something wrong!');




define('DEBUG', 			true);

// Database Auth Params

define('DBUSER', 			'jcmax');
define('DBPASS', 			'121212');
define('DBCHAR', 			'utf8');
define('DBHOST', 			'localhost');
define('DBNAME', 			'ModuleCMS');
define('DBPREF', 			'_prefx');

define('DBENGINE', 			'mysql');



// сгенерировать при установке

define('SOLT',				'abcabcabc'); 

define('PRIVATEKEY', 		'mycoolprivatekeytohidesomething');
define('PUBLICKEY', 		'mymorecoolpublickeytohidesomething');


define('WEBTITLE',			'ModuleCMS');
define('WEBTITLEDESC',		'Module description!');

define('DEFLANGUAGE', 		'Ru-ru');

define('DEFROUTEPATH', 		'Routes'.DS);
//TODO: Нужно отфильтровать
define('HOST', 				'http://'.$_SERVER['HTTP_HOST']);
define('HOSTENABLED', 		true);
define('HOSTREDIRECT',		'');

define('TPLFOLDER',			ROOTPATH.'Templates'.DS);
define('TPLTEMPLATE',		'bootstrap'.DS);
define('TPLSCHEMEFILE',		'schema.tpl.php');

define('REGISTRATIONALLOW',	true);
define('RESTOREALLOW', 		true);

define('LOGINALLOW',		true);
define('LOGINREDIRECT',		true);
define('LOGINREDIRTIMEOUT',	0);
define('LOGINREDIRREFFERER',false);

define('LOGOUTALLOW', 		true);
define('LOGOUTREDIR', 		true);
define('LOGOUTREDIRTIMEOUT',3);
define('LOGOUTREDIRPATH', 	HOST);

define('AUTHALLOW', 		true);
// Установка куки на короткий период в часах
define('AUTHCOOKIEUPDATE', 	2);
// Установка куки на долгий период в часах
define('AUTHCOOKIEUPDLONG', 24 * 7);
define('AUTHUPDATEPASSINT', 70);
define('AUTHWAITREGTIME',	'+24 Hours');
define('AUTHHASHUPDATETIME', 7);

define('SQLITEFOLDER', 		'sqlitefolder');
define('SQLITEFILE',		'sqlitefile.db');

// Диапазон размера генерации хеш числа 
define('HASHMINVALUE',		30);
define('HASHMAXVALUE',		100);


define('ADMINISTRATOR', 	1);
define('MODERATOR', 		2);
define('AUTHOR', 			3);
define('SUBSCRIBER', 		4);
define('BLOCKED', 			5);
define('DELETED', 			6);
define('VISITOR', 			7);


/*
Базовые настройки привелегий 
------------------
1. Администратор 
	Имеет полный доступ ко всем страницам
	главные настройки вебсайта и управлению
------------------
2. Редактор 
	Имеет доступ редактировать свои или чужие данные 
	например посты/коментарии/профили пользователей
------------------
3. Автор 
	Имеет право создавать устанавливать темы, создавать 
	свои посты, коментарии и имеет право редактировать их
------------------
4. Подписчик
	Имеет ограниченные права в определенных подписаных страницах. имеет право оставлять коментарии или редактировать их.
------------------
5. Заблокирванный пользователь
	Имеет право авторизироваться, но все функции отключены.
	не имеет право ни на какое действие только чтение.
------------------
6. Удаленный пользователь
	Зарегестрированный пользователь, который был удален
	данные пользователя остаются в системе определенное время, но зайти пользователь уже может
------------------
7. Гость или не зарегестрированный пользователь
	Не имеет прав, так, не определен системой,
	не идентифицированный пользователь
------------------
*/
