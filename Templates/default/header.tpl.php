<!DOCTYPE html>
<html lang="en">
<head>
	<title>Авторизация</title>
	<link rel="stylesheet" href="/Templates/default/css/bootstrap.min.css">
	<script src="/Templates/default/js/jquery-3.4.1.slim.min.js"></script>
	<script src="/Templates/default/js/popper.min.js"></script>
	<script src="/Templates/default/js/bootstrap.min.js"></script>
</head>
<body style="margin: 50px;">

<?php 
	if(defined('PROFILE') && !empty(PROFILE)) {
		echo '<pre>----------------------------------';
		debugger(PROFILE, 'Профиль пользователя');
		echo '<p><a href="/logout">Выйти</a></p>';
		echo '----------------------------------</pre>'; 
	}

	echo '<a href="/usersonline">Кол-во пользователей на сайте: '.$res['/online']['result'].'</a>';

	if(!empty($metadata)) {

		foreach ($metadata as $key => $value) {
			
			debugger($value, 'Параметры из запущенного контроллера!');
		}
	}

