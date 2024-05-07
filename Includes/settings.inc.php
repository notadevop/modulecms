<?php

defined('ROOTPATH') and defined('DS') or die('something wrong');

return Array(
  'database' => Array(

    // База данных подключение
    'dbuser'    => 'jcmax',
    'dbpass'    => '121212',
    'dbchar'    => 'utf8',
    'dbhost'    => 'localhost',
    'dbname'    => 'ModuleCMS',
    'dbpref'    => '_modcmsprefix',
    'dbengine'  => 'mysql',
  ),

  'tokens' => Array(
    'solt'        => 'here_is_random_hash_value',
    'private_key' => 'here_is_private_hash_data_generated!',
    'public_key'  => 'here_is_public_hash_data_generated!',

  ),
  'website_settings' => Array(
    'website_title'           => 'ModuleCMS',
    'website_title_descr'     => 'ModuleCMS Description',
    // сгенерировать при установке, либо 'http://'.$_SERVER['HTTP_HOST']);
    'website_hostname'        => 'http://localhost',
    'website_host_enabled'    => true,
    'website_host_redirect'   => false,
    'website_locale_default'  => 'Ru-ru',
    'website_default_template'=> 'bootstrap',
    'template_index_file'     => 'schema.tpl.php',
  ),
  'paths' => Array(
    'language_folder'   => 'LangLibrary',
    'user_routes_folder'=> 'Routes',
    'templates_folder'  => 'Templates',
    'sqlite_folder'     => 'sqlitefolder',
    'sqlitefile'        => 'sqlitefile.db',
  ),
  'limits' => Array(
    'hash_min_value'  => 30,
    'hash_max_value'  => 100,
  ),

  'authoriziation' => Array(
    'registration_allow'  => true,
    'restore_allow'       => true,
    'login_allow'         => true,
    'login_redirect'      => true,
    'login_redirect_time' => 0,     // in seconds
    'login_redir_refferer'=>false,

    'logout_allow'      => true,
    'logout_redir'      => true,
    'logout_redir_time' => 3,
    'logout_redir_host' => 'localhost',

    'auth_allow'          => true,
    'auth_cookie_update'  => 2,
    'auth_cookie_upd_long'=> 24 *7,
    'auth_upd_pass_inter' => 70,
    'auth_wait_reg_time'  => '+24 Hours',
    'auth_upd_hash_time'  => 7,
  ),

  'priveleges'  => Array(
    'administrator' => 1,
    'moderator'     => 2,
    'author'        => 3,
    'subscriber'    => 4,
    'blocked'       => 5,
    'deleted'       => 6,
    'guest'         => 7,
  ),
);












/*
// Database Auth Params

define('DBUSER', 			'jcmax');
define('DBPASS', 			'121212');
define('DBCHAR', 			'utf8');
define('DBHOST', 			'localhost');
define('DBNAME', 			'ModuleCMS');
define('DBPREF', 			'_MCMSprefix');
define('DBENGINE', 			'mysql');

// сгенерировать при установке

// Инициализировать при установке !
define('DEBUG', 			  true);
define('SOLT',				  'abcabcabc');
define('PRIVATEKEY', 		'mycoolprivatekeytohidesomething');
define('PUBLICKEY', 		'mymorecoolpublickeytohidesomething');

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
define('AUTHCOOKIEUPDATE', 	2);
// Установка куки на долгий период в часах
define('AUTHCOOKIEUPDLONG', 24 * 7);
define('AUTHUPDATEPASSINT', 70);
define('AUTHWAITREGTIME',	'+24 Hours');
define('AUTHHASHUPDATETIME', 7);

define('WEBTITLE',			'ModuleCMS');
define('WEBTITLEDESC',		'Module description!');

define('DEFLANGUAGE', 		'Ru-ru');

define('DEFROUTEPATH', 		'Routes'.DS);

//TODO: Нужно отфильтровать
// Тут нужно использовать хост который будет сохранен в базе данных !!!!
// Можно инилизировать в классе

define('HOST', 				'http://'.$_SERVER['HTTP_HOST']);
define('HOSTENABLED', 		true);
define('HOSTREDIRECT',		'');

define('TPLFOLDER',			ROOTPATH.'Templates'.DS);
define('TPLTEMPLATE',		'bootstrap'.DS);
define('TPLSCHEMEFILE',		'schema.tpl.php');

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
*/


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



class StoredSettings {

	// Просто хранит настройки в классе

	// Нужно отделить настройки которые будут конфигурироваться или доставаться из базы при runtime'ме

	// и те что будут сгенерированны при первом запуске

	// в сonfig.inc.php  <-- так, как в класс их не запихнуть

	// иницилизировать определенные типы настроек

}
