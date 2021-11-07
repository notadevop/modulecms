<?php 

// 1. проверяем на авторизацию
// 2. смотрим есть вообще напоминания
// 3. выводим сообщение 


require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);
?>

  <section id="pageContent">
		<main role="main">
			<article>
				<h2>Уведомления</h2>

				<?=debugger($this->result['templateRes']);?>
			</article>
		</main>




<?php 
require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);