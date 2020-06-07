<?php 

	debugger($tplRes,'ПРОФИЛЬ ПОЛЬЗОВАТЕЛЯ!');
?>

<p>
	<form action="" method="POST">
		
		<p>
			<input type="text" name="useremail" placeholder="Емайл пользователя" value=""/>
		</p>
		<p>
			<input type="text" name="username" placeholder="Имя пользователя" value=""/>
		</p>

		<p>
			<input type="text" name="useroldpass" placeholder="Пароль пользователя" value=""/>
		</p>
		<p>
			<input type="text" name="usernewpass1" placeholder="Новый Пароль пользователя" value=""/>
		</p>
		<p>
			<input type="text" name="usernewpass2" placeholder="Новый Пароль пользователя" value=""/>
		</p>
		<p>
			<input type="submit" name="actionButton" value="Обновить"/>
		</p>

	</form>

