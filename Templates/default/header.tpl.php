<!DOCTYPE html>
<html lang="en">
<head>
	<title>Авторизация</title>
	<link rel="stylesheet" href="/Templates/default/css/bootstrap.min.css">
	<script src="/Templates/default/js/jquery-3.4.1.slim.min.js"></script>
	<script src="/Templates/default/js/popper.min.js"></script>
	<script src="/Templates/default//js/bootstrap.min.js"></script>
</head>
<body style="margin: 50px;">

<a href="/usersonline">Онлайн пользователи</a>

<?php 
	if(defined('PROFILE') && !empty(PROFILE)) {
		echo '<a href="?logout=1">Выйти</a>';
	}

	if(!empty($metadata)) {

		foreach ($metadata as $key => $value) {
			
			echo '<p>'.debugger($value).'</p>';
		}
	}

