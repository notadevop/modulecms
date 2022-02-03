<?php 

defined('ROOTPATH') or die();

return array(

	'templates' => array(
		'header' 	=> 'subtpl/header.tpl.php',
		//'banner' 	=> 'subtpl/banner.tpl.php',
		//'sidebar' => 'subtpl/sidebar.tpl.php',
		'footer' 	=> 'subtpl/footer.tpl.php',
	),
	'css' => array(
		'css1'		=> 'css/style.css',
	),
	'pages' => array(

		'default'  => 'infopage.tpl.php',
	),
	'languagePack' => array(
		'rus' 		=> 'langpack/rus.lang.php',
		'eng'		=> 'langpack/eng.lang.php'
	)
);