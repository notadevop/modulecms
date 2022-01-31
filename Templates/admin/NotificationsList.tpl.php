<?php 

defined('ROOTPATH') or die();

// 1. проверяем на авторизацию
// 2. смотрим есть вообще напоминания
// 3. выводим сообщение 


require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);
?>

  <section id="pageContent">
		<main role="main">


			<?php 
			if (($this->result['templateRes']) > 0) {
					?>
				<article>
				<h2>Уведомления</h2>
					<table class="table">
					  <thead>
					    <tr>
					      <th scope="col"></th>
					      <th scope="col">Уведомление</th>
					      <th scope="col">Meta</th>
					      <th scope="col">Дата</th>
					      <th scope="col"></th>
					    </tr>
					  </thead>
					  <tbody>

					  	<?php 

							foreach ($this->result['templateRes'] as $key => $value) {
			  			?>
					  	
					  		<tr <?=$value['notif_read'] == 0 ? 'class="table-primary"' : '';?>>
						      <th scope="row"><?=$value['notif_read'] == 0 ? '✴' : '';?></th>
						      <td><a href="/admin/"><?=$value['notif_title']; ?></a></td>
						      <td><?=$value['notif_meta']; ?></td>
						      <td><?=date('F j, D, Y, g:i a', $value['notif_date']); ?></td>
						      
						      <td>
						      	<form action="" method="POST"> 
						      		<input type="hidden" name="notifid" value="" />
						      		<input type="submit" name="" value="✖" />
						      	</form>
						      </td>
						    </tr>
						  
				  			<?php 
			  			}
					  	?>
					  </tbody>
					</table>
				</article>
					<?php 
			}
			?>
			
		</main>

<?php 
require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);