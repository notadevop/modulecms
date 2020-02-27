

	<form action="/auth/register" method="POST">
		<input type="text" name="userregname" value="maksim <?php echo rand(1,10000) ?>" />
		<br /><br />
		<input type="text" name="userregemail" value="jcmax<?php echo rand(1,10000) ?>@gmail.com" />
		<br /><br />
		<input type="text" name="userregpassword1" value="Hesoyam12" />
		<br /><br />
		<input type="text" name="userregpassword2" value="Hesoyam12" />
		<br /><br />
		<input type="submit" name="Registeraction" value="Регистрация" />
	</form>