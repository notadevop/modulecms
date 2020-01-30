<!DOCTYPE html>
<html>
<head>
	<title>Авторизация</title>
</head>
<body>

<?php 
	if(defined('PROFILE') && !empty(PROFILE)) {
		echo '<a href="?action=login&logout=1">Logout</a>';
	}
?>

