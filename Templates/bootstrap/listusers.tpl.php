<?php 
/**
 *
 * 
 */


$users = $this->result['templateCtrlResult']['result'];
?>

<section id="pageContent">
    <main role="main">
	     <table class="table">
		  <thead>
		    <tr>
		      <th scope="col">ID</th>
		      <th scope="col">Имя</th>
		      <th scope="col">Емайл</th>
		      <!--<th scope="col">Время Регистрации</th>-->
		      <th scope="col">Последний визит</th>
		      <th scope="col">Статус</th>
		    </tr>
		  </thead>
		  <tbody>

		  	<?php 

		  	foreach ($users['users'] as $key => $value) {
		  		?>
				    <tr>
				      <th scope="row"><?=$value['user_id'];?></th>
				      <td><?=$value['user_name'];?></td>
				      <td><?=$value['user_email'];?></td>
				      <!--<td><?=$value['user_registration_date'];?></td>-->
				      <td><?=date('m.d.y' , $value['user_last_visit']);?></td>
				      <?php
				      if ($value['user_activated'] == 1) {
				      	echo '<td class="table-success">Активирован</td>';
				      } else {
				      	echo '<td class="table-warning">Не активирован</td>';
				      }
				      ?>
				    </tr>
		  		<?php 
		  	}
		  	?>
		  </tbody>
		</table>
    </main>