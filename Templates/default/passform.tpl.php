
<?php 
	debugger($result['templContrResult'], 'Форма пароля');
?>

	<!-- ПОменять потом -->
	
	<form action="/confpass/?<?=http_build_query($metadata['result']);?>" method="POST">
		<p>Новый пароль пользователя</p>
		<input type="text" name="newpassword1" value="" placeholder="Твой новый пароль!" />
		<br /><br />
		<p>Повторить новый пароль пользователя</p>
		<input type="text" name="newpassword2" value="" placeholder="Повторить пароль!" />
		<br /><br />
		<input type="submit" name="Restoreaction" value="Обновить пароль" />
	</form>