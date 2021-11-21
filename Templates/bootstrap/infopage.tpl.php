<?php 
/**
 * Тело шаблона
 */


require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);

?>

  <section id="pageContent">
		<main role="main">
				<h2>Информационная страница или заглушка сайта</h2>
				<hr />
				<p>Данная страница несет только информационный характер!</p>
		</main>

<?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);