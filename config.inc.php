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


// Время данное для подтверждение регистрации пользователя 
define('REGWAITER', '+24 Hours');



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


















