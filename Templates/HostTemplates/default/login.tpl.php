<?php 
	debugger($result['templContrResult'], 'Окно входа');
?>

	<form action="/login" method="POST">
		<input type="text" name="loginmail" value="jcmaxuser@gmail.com" />
		<br />
		<input type="text" name="loginpasswd" value="Hesoyam12" />
		<br />
		<input type="submit" name="loginaction" value="Login" />
	</form>

	