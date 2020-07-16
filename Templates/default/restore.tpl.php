<?php 

	debugger($result['templContrResult'], 'Восстановление');
?>

	<form action="/restore" method="POST">
		<input type="text" name="restoremail" value="jcmaxuser@gmail.com" />
		<br /><br />
		<input type="submit" name="Restoreaction" value="Отправить запрос" />
	</form>