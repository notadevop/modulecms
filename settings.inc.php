<?php 

define('sqliteFolder', 'sqlitefolder');
define('sqlitefile', 'sqlitedb.sql');


// Тут хронятся все настройки веб сайта, 
// отличие от config в том, что это статические настройки веб сайта

// Разрешаем авторизацию, аутентификацию пользователя

define('AUTHENTIFCATIONALLOW', true);
define('REGISTRATIONALLOW', true); // <=== 
define('LOGINALLOW', true);
define('RESTOREALLOW',true);


define('DEFROUTEPATH', 'Routes' . DS);

define('REDIRECTLOGIN', TRUE);


define('AllowLoginRedirect', true); // При правильной авторизации перенаправлять пользователя
define('LoginRedirectPath', '/profile/');	

// нужно для того, чтобы отдельно указывать другой путь шаблону
define('TPLDEFAULTFOLDER', ROOTPATH . 'Templates'.DS); 

define('TPLDEFAULTTEMPLATE', 'default'.DS);

define('SOLT','abcabcabc'); // сгенерирован при установке

define('PRIVATEKEY', 'mycoolprivatekeytohidesomething');
define('PUBLICKEY', 'mymorecoolpublickeytohidesomething');

define('UPDATEAUTHINTERVAL', 7); // Интервал обновления хеша аутентификации 
define('UPDATEPWDINTERVAL', 70); // Интервал обновления пароля пользователя в днях
 

//define('DS', DIRECTORY_SEPARATOR);

define('HOST', 'http://'.$_SERVER['HTTP_HOST']);


// Время данное для подтверждение регистрации пользователя 
define('REGWAITER', '+24 Hours');



// НАСТРОЙКИ: НИЖЕ

// Константа массива для работы с базой данных sqlite 
define('SQLITEJOB', array(
	'sqlitefolder' 	=> 'sqlitefolder',
	'sqlitefile'	=> 'tempdb.sqlite'
));


define('REDIRECTORS', array(
	'defPostLogin' 	=> 'profile',
	'defPostReg'	=> '',
	'defPostRest'	=> ''
));

/*
	LOGIN 		- откуда человек пришел в пределах сайта
	RESTORE 	- после подтверждения, отправить на главную страницу
	REGISTER 	- после подтверждения регистрации на профиль страницы
	AUTH 		- проверяет скрытно
*/

/*
Привелегии пользователя могут иметь несколько привелегий .... !!!!

 1. administrator, 		Имеет все привелегии
 2. moderator/editor  	Модератор/Редактор может редактировать, свои/чужие посты, удалять в карзину посты, редактировать всех коментарии
 3. author,  			Автор, Может добавлять/удалять только свои посты и коментарии.
 4. subscriber  		Подписчик может добавлять/удалять только свои коментарии
 5. blockeduser			Заблокированный пользователь, Временно не доступный пользователь, не имеет доступа и авторизации 
 6. deleteduser 		Удаленный пользователь, вход в систему запрещен пожизнено. 
 7. visitor				Гость, не определенный пользователь
*/

define('ADMINISTRATOR', 1);
define('MODERATOR', 2);
define('AUTHOR', 3);
define('SUBSCRIBER', 4);
define('BLOCKED', 5);
define('DELETED', 6);
define('VISITOR', 7);



/*
	Фильтрация данных 

	Легкая - Фильтрация от Mysql Injection, XSS. 

	Средняя - Фильтрация от Mysql Injection, XSS, Теги конвертирует

	Тяжелая - Фильтрация от Mysql Injection, XSS, Теги удаляет, и все доступные фильтры которы могут быть
 */