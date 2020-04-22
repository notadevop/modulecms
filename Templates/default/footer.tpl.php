
<?php 

$links = array(

	'/' 					=> 'Главная',
	'/login' 				=> 'Войти',
	'/register' 			=> 'Регистрация',
	'/restore' 				=> 'Забыл пароль',
	'/phpmyadmin/'			=> 'Phpmyadmin',
	'http://phptester.net'	=> 'php sandbox',
	'/users'				=> 'Зарегестрированные пользователи'
);




$i = 0;

foreach ($links as $key => $value) {
	
	if($i >= 5) { 
		$i = 0;
		echo '<br/>';
	}

	echo '<a href="'.$key.'">'.$value.'</a> | ';

	$i++;
}
?>
</br>
</body>
</html>
