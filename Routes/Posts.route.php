<?php

// файл путей постов

$routes = array(

	'/posts/:num' 	=> array(
			'action' 	=> 'MainController/test/$1',
			'template'	=> 'infopage.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'skipUri' 	=> false, // Permanent or not if yes, !!!
			'decript'	=> 'Cтраница постов',
			'priority'	=> 2
	),
);
