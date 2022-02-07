<?php 

defined('ROOTPATH') or die();

/**
 *
 * 
 */

/*
if(!$this->regOk || empty($this->result['templateRes'])) { 

	require_once($this->activeTpl.$r['templates']['header']);
	require_once($this->activeTpl.$r['templates']['banner']);
	?>
	<section id="pageContent">
	    <main role="main">Страница пустая</main>

	<?php 
	require_once($this->activeTpl.$r['templates']['sidebar']);
	require_once($this->activeTpl.$r['templates']['footer']);
	return 1; 
}
*/

$users = $this->result['templateRes'];

require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);


?>

<section id="pageContent">
    <main role="main">
	     <table class="table table-striped">
		  <thead>
		    <tr>
		      <th scope="col"></th>
		      <th scope="col">Профиль</th>
		      <!-- <th scope="col">Емайл</th> -->
		      <!--<th scope="col">Время Регистрации</th>-->
		      <th scope="col">Посещение</th>
		      <th scope="col">Статус</th>
		      <th scope="col"></th>
		      <th scope="col"></th>
		      <th scope="col"></th>
		      <th scope="col"></th>
		    </tr>
		  </thead>
		  <tbody>

		  	<?php 
		  	foreach ($users['users'] as $key => $value) {

		  		$profile = str_replace(':num', $value['user_id'],$this->allRoutes['/profile/:num']['url']);

		  		?>
				    <tr>
				      <th scope="row"><?=$value['user_id'];?></th>
				      <td><a href="<?=$profile;?>"><?=$value['user_name'];?></a></td>
				      <!-- <td><?=$value['user_email'];?></td>-->
				      <!--<td><?=$value['user_registration_date'];?></td>-->
				      <td><?=date('F j, Y, g:i a' , $value['user_last_visit']);?></td>
				      <td>
				      <?php
				      echo $value['user_activated'] == 1 ?
				      '<b><span class="link-success">Активирован</span></b>' :
				      '<b><span class="link-warning">Не активирован</span></b>';
				      ?>
				      </td>
				      <td><a href="<?=str_replace(':num', PROFILE['userid'] ,$this->allRoutes['/profile/edit/:num']['url']);?>">Редактировать</a></td>
				      <td><a href="">Написать</a></td>
				      <td><a href=""  class="link-warning">Заблокировать</a></td>
				      <td><a href="" class="link-danger" >🗑</a></td>
				    </tr>
		  		<?php 
		  	}
		  	?>
		  </tbody>
		</table>
    </main>

 <?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);