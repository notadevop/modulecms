
<?php 

foreach ($params as $key => $value) {
	
	if (!$value['skipUri'] && $value['priority'] == 4) {

		echo '<a href="'.$key.'">'.$value['decript'].'</a> | ';
	
	} if (defined('PROFILE') && !empty(PROFILE['useremail']) && !$value['skipUri'] && ($value['priority'] == 3 )) { 

		echo '<a href="'.$key.'">'.$value['decript'].'</a> | ';
	}
}

$extUrls = array(

		'/phpmyadmin/' 			=> 'PHPMYADMIN',
		'http://phptester.net' 	=> 'PHPTESTER.NET'
);

echo '<br/><hr/>';

foreach ($extUrls as $key => $value) {
	
	echo '<a href="'.$key.'" target="_blank">'.$value.'</a> | ';
}

?>
</br>
</body>
</html>
