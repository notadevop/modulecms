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

	if($regOk) {

		$name 	= '<a href="/profile">'.PROFILE['username'].'</a>';
		$logout = '<a href="/logout">Выйти?</a>';
		$online = '<a href="/usersonline">('.$result['permContrResult']['online']['result'].')</a>';
	} else {
		$name = 'Гость';
		$logout = '';
		$online = '('.$result['permContrResult']['online']['result'].')';
	}
?>

	<p>Вы вошли на сайт как:(<?=$name; ?>), <?=$logout;?>

	Онлайн: <?=$online;?></p>
	