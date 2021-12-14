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
				<h2>Административная заглушка!</h2>
				<p></p>
		</main>

<?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);