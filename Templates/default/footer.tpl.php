
<?php 

$links = array(

	'/' 				=> 'Главная',
	'/auth/login' 		=> 'Войти',
	'/auth/register' 	=> 'Регистрация',
	'/auth/restore' 	=> 'Забыл пароль',
	'/phpmyadmin/'		=> 'Phpmyadmin',
	'http://phptester.net'=> 'php sandbox'
);

foreach ($links as $key => $value) {
	
	echo '<a href="'.$key.'">'.$value.'</a> | ';
}
?>
</br>
</body>
</html>
