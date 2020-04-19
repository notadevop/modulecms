
<?php 

$links = array(

	'/' 					=> 'Главная',
	'/login' 				=> 'Войти',
	'/register' 			=> 'Регистрация',
	'/restore' 				=> 'Забыл пароль',
	'/phpmyadmin/'			=> 'Phpmyadmin',
	'http://phptester.net'	=> 'php sandbox'
);

foreach ($links as $key => $value) {
	
	echo '<a href="'.$key.'">'.$value.'</a> | ';
}
?>
</br>
</body>
</html>
