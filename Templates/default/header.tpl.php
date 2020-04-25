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

	echo '<p>Вы вошли на сайт как: ';

	if(defined('PROFILE') && !empty(PROFILE['useremail'])) {

		echo '<a href="/profile">('.PROFILE['username'].')</a>  <a href="/logout">Выйти?</a></p>';
	} else {
		echo '(Гость)</p>';
	}

	echo 'Пользователей: <a href="/usersonline">('.$res['online']['result'].')</a>';

	if(!empty($metadata)) {

		debugger($metadata, 'Это Шаблон загаловка!');
	}

