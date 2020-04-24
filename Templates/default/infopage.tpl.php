<h2> Информационная страница</h2>

<p>меню страницы</p>

<?php 

	if(!empty($metadata['errors'])) {

		foreach ($metadata['errors'] as $key => $value) {
			echo '<p>';
			print_r($value);
			echo '</p>';
		}

		//debugger($metadata, 'Это Шаблон загаловка!');
	}
