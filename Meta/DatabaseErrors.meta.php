<?php 


// DATABASE ERRORS

define('DB_WRONG_AUTH', 		'Ошибка, Соединение к базе невозможно, неправильный имя или пароль!');
define('DB_CONNECT_FAILURE',	'Ошибка, соединения к базе данных!')
define('SQL_NOT_SET',			'Ошибка, параметра SQL, Переменная пустая!');
define('SQL_PREP_FAILURE', 		'Ошибка, подготовки sql запроса!');
define('SQL_EXEC_FAILURE', 		'Ошибка, исполнения sql запроса!');

// PRIVELEGES ERRORS 

define('PRIV_NOT_ENOUGH',		'Ошибка, У вас недостаточно привелегий!');
define('CANNOT_UPD_DATE',		'Ошибка, обновления даты!');

// AUTH ERRORS

define('HASH_GEN_ERR', 			'Ошибка, генерации хеша авторизации!');
define('COOKIE_ERR_SAVE',		'Ошибка, не могу установить куки в браузере!');
define('COOKIE_MISSING', 		'Ошибка, куки отсутсвуют');

// FILESYSTEM ERRORS 

define('FILE_MIMETYPE_ERR', 	'Ошибка, запрещенные разрещения файлов!');
define('FILE_PARAM_ERR', 		'Ошибка, Некорректный параметр!');
define('FILE_NO_SENT',			'Ошибка, Файл не отправлен!');
define('FILE_SIZE_EXCEEDED',	'Ошибка, Размер файл превышен разрешенный лимит!');
define('FILE_UNKNOWN_ERR',		'Неизвестная ошибка!');
define('FILE_INVALID_FORMAT',	'Ошибка, неверный формат файла');

define('FILE_UPL_SUCCESS',		'Файл загружен!');

// FILTER ERRORS 

define('FIELD_EMPTY',			'Ошибка, у вас есть пустые поля!');
define('FIELD_MAX_SYMB',		'Разрешенное максимальное количество символов:');
define('FIELD_MIN_SYMB',		'Разрешенное минимальное количество символов:');
define('FIELD_EMAIL_INVALID',	'Ошибка, указан некорректный емайл!');
define('FIELD_NUMBERS_ONLY', 	'Ошибка! Значение нужно указать цифровым');

// PAGENAV ERRORS 

define('PAGE_VALUE_INCORRECT',	'Ошибка, неверное значение на страницу!');
define('PAGE_CURRENT_WRONG',	'Ошибка, неверный номер текущей страницы');

define('ROUTE_ERROR', 			'Ошибка, пути не найдены!');

// PROFILE ERRORS 

define('USER_REND_ERR',			'Ошибка, не могу показать пользователя!');
define('USER_MISSING',			'Ошибка, пользователь удален!');
define('USER_LIST_ERR', 		'Ошибка, не могу вывести список пользователей!');
define('USER_PASS_WRONG',		'Ошибка, имя или пароль не верные!');
define('USER_BLOCKED',			'Ошибка, пользователь заблокирован!');
define('USER_DELETED',			'Ошибка, пользователь удален!');
define('USER_PARAMS_ERR',		'Ошибка, параметров подтверждения пользователя!!');
define('USER_PASS_MISMATCH',	'Ошибка, пароли не совпадают!');
define('USER_PASS_UPDATE_ERR',	'Ошибка, обновления пароля!');
define('USER_EXIST', 			'Ошибка, пользователь уже существует!');
define('USER_REGISTER_ERR', 	'Ошибка, регистрации пользователя!');
define('USER_ACTIV_ERR',		'Ошибка, активации пользователя!');

define('USER_LOGGED', 			'Вы вошли в систему!');
define('USER_PASS_UPDATED', 	'Пароль пользователя обновлен!');
define('USER_META_UPDATED', 	'Данные пользователя обновлены!');
define('USER_ACTIVED',			'Пользователь активирован!');

