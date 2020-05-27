<?php



$routes = array(

	'/' 		=> array(

			'action' 	=> 'MainController/defaultMethod',
			'template'	=> 'template.tpl.php',
			'ifRegOk'	=> 'infopage.tpl.php',
			'permanent' => false,
	),


);
