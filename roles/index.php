<?php

/*
ROLES  
	-role_id 
	-role_name -> (admin, author, editor, user)

PERMISSIONS
	-perm_id 
	-perm_desc (Все, автор, редактирование, пользователь)

ROLE_PERM
	-role_id (Зависит от устан. роли)
	-perm_id (Зависит от устан. прав)

USER_ROLE 
	-user_id (зависит от Id зарег. пользоват.)
	-role_id (зависит от Id установ. роли)

Скрипт содержит в Role.php 

	Вытаскивает права самой роли 
	Определяет установленны ли права	
	Добавляет роль
	Удаляет роль 
	Добавляет роли для определенного пользователя
	Удаляет роли пользователей
	

Скрипт содержит в PrivelegedUser.php
	
	Вытаскаивает роли по пользователю
	Объеденяет определенные роли с их правами 
	Проверяет есть ли у пользователя указанные привелегии 
	Проверяет есть ли у пользователя указанные роли 
	Добавляет права 
	Удаляет права 
*/

require_once "Role.php";
require_once "PrivilegedUser.php";

$username = 'user';
$userperms = "permissions name";

if (isset($username)) {

    $u = PrivilegedUser::getByUsername($username);

    if ($u->hasPrivilege($userperms)) {
    	
    	echo "You are logged with this permission ".$userperms;
    } else {

   		echo 'forbidden you don\' have permission to access this webage!';
    }
} else {

	echo "You'r not authentificated!";
}


