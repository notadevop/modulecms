
<?php 

foreach ($params as $key => $value) {
	
	if (!$value['skipUri'] && $value['priority'] == 4) {

		echo '<a href="'.$key.'">'.$value['decript'].'</a> | ';
	}

}

$extUrls = array(

		'/phpmyadmin/' 			=> 'PHPMYADMIN',
		'http://phptester.net' 	=> 'PHPTESTER.NET'
);

echo '<br/>';

foreach ($extUrls as $key => $value) {
	
	echo '<a href="'.$key.'">'.$value.'</a> | ';
}

?>
</br>
</body>
</html>
