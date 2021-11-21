<?php 

// Ошибки связанные с базой данных

define('DBERRCONN', 		'Ошибка подключения к базе данных!');
define('DBENGINEERR', 		'Движок может работать с MYSQL базой данных!');
define('DBERRINFO', 		'Ошибка подключения к базе обратитесь к администратору');
define('DBEMPTYSQL', 		'Ошибка не установлен SQL запрос!');
define('DBERRPREPQUERY',	'Ошибка подготовки SQL запроса!');
define('DBERRQUERY', 		'Ошибка исполнения SQL запроса!');

define('ERRCLASSNOTFOUND',	'Класс %s не найден!');
define('NOROUTES',			'<h1>Фатальная ошибка пути не найдены!</h1>');
define('ROUTEAFFECTED',		'<h1>Индексы путей поврежденны!</h1>');

define('NOLANGUAGEPACK', 	'Языковый пакет не найден');


// Ошибки связанные с авторизацией

define('LOGINDISABLED', 	'Вход в систему отключен администратором!');
define('ERREMAILWRONG', 	'Неправильный емайл или пароль!');

define('ERRGENHASH', 		'Ошибка генерации хеш кода!');
define('ERRSAVEMETA', 		'Ошибка! не могу сохранить данные, проверьте куки!');
define('AUTHDISABLED',		'Авторизация в системе отключена администратором!');
define('RESTOREDISABLED',	'Восстановление профиля отключено администратором!');

define('AUTHPARAMSERR',		'Ошибка параметров подтверждения пользователя!');
define('VERIFYNOTFOUND',	'Проверка не пройдена!');
define('PWDNOTMATCH',		'Ошибка! пароли не совпадают');
define('PWDUPDERR',			'Ошибка обновления пароля!');

define('REGDISABLED',		'Регистрация отключено администратором!');

define('ERRGENLINK',		'Ошибка генерации ccылки для активации!');
define('REGATRRERR',		'Параметры регистрационных данных не правильные или отсутсвует!');

define('EMPTYFIELDSEXIST',	'У вас есть пустые поля!');
define('ERRMAXSYMLIMIT',	'Ошибка! Максимальное разрешенное число символов %s');
define('ERRMINSYMLIMIT',	'Ошибка! Минимальное разрешенное число символов %s');
define('ERRMAIL',			'Указан некорректный емайл!');

define('USERACTERR',		'Ошибка активации пользователя!');
define('ADDUSERERR',		'Ошибка! Не могу добавить пользователя.');
define('USEREXIST',			'Такой пользователь уже зарегестрирован!');
define('ACTUSERERR',		'Не смог удалить активационные данные!');
define('USERNOTFOUND',		'Указанный пользователь не найден! (возможно удален)');
define('USERBANNED', 		'Указанный пользователь отправлен в бан!');
define('NOLISTUSERS', 		'Не могу вывести список пользователей!');
define('USERNULL',			'Некорректный или отсутствует ID пользователя!');

define('NOPRIVELEGES', 		'Ошибка, У вас недостаточно привелегий');

define('NOTEMPLETEFOUND', 	'Шаблон не найден!');
