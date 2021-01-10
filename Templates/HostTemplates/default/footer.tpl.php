
<?php 



foreach ($routes as $k => $v) {
	
	if (!$v['skipUri'] && $v['priority'] == 4) {

		echo '<a href="'.$k.'">'.$v['decript'].'</a> | ';
	} else if ($regOk && !$v['skipUri'] && ($v['priority'] == 3 )) { 

		echo '<a href="'.$k.'">'.$v['decript'].'</a> | ';
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
