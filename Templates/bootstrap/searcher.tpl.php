<?php 

defined('ROOTPATH') or die();

/**
 * Тело шаблона
 */


require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);

?>

  <section id="pageContent">
		<main role="main">
				<h2>Поиск по вебсайту со стороны пользователя</h2>
				<hr />
				<p>Осуществлять поиск только по таблицам которые открыты как public </p>

<pre>
// Вариант поиска 1.
// создать таблицу для поиска и туда вводить методы которые могу по поиску отдавать данные
// сам поиск обращается к базе данных с этой таблицей 
// и по ней выводит таблицу с этими методами 
// и делает запрос у них к тем данным которые они запрашивают

// Вариант поиска 2.
// Осуществлять поиск только по тому месту где конкретно пользователь сейчас находиться 
// /post/123?comments=1  <= будет искать толко в посте 123 и его коментариях
</pre>

				<?php 

				vardump($_GET);
				?>
		</main>

<?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);