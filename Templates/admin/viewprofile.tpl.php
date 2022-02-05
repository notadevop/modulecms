<?php 

defined('ROOTPATH') or die();

/**
 * 
 */

/*
if(!$this->regOk || empty($this->result['templateRes'])) { 

	require_once($this->activeTpl.$r['templates']['header']);
	require_once($this->activeTpl.$r['templates']['banner']);
	?>
	<section id="pageContent">
	    <main role="main">Пустой профиль</main>

	<?php 
	require_once($this->activeTpl.$r['templates']['sidebar']);
	require_once($this->activeTpl.$r['templates']['footer']);
	return; 
}
*/
require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);

?>

<section id="pageContent">
    <main role="main">
	    	<h2>Пользовательский профиль</h2>
	    	<p><?=$this->result['templateRes']['userpicture'];?></p>

	    	 <caption>Данные пользователя</caption>
	    	<table class="table table-sm">
			
	    	
			  <thead>
			    <tr>
			      <th scope="col"></th>
			      <td scope="col"></td>
			      <td scope="col"></td>
			    </tr>
			  </thead>
			  <tbody>
			  	<tr>
			      <th scope="col"></th>
			      <td scope="col">ID Пользователя</td>
			      <td scope="col"><?=$this->result['templateRes']['id'];?></td>
			    </tr>
			  	<tr>
				  	<th scope="row"></th>
				  	<td>Имя пользователя</td>
				  	<td>@<?=$this->result['templateRes']['name'];?></td>
			  	</tr>
			  	<tr>
			      <th scope="row"></th>
			      <td>Cтатус пользователя</td>
			      <td>
				  	<?php 
				  	echo $this->result['templateRes']['actstatus'] == 1 
				  	? '<b><span class="link-success">Активирован!</span></b>' :
				  	'<b><span class="link-warning">Не Активирован!</span></b>';
				  	?>
			      </td>
			    </tr>
			    <tr>
			      <th scope="row"></th>
			      <td>Пользовательский емайл</td>
			      <td><?=$this->result['templateRes']['email'];?></td>
			    </tr>
			    <tr>
			      <th scope="row"></th>
			      <td>Дата регистрации</td>
			      <td><?=date('F j, D, Y, g:i a' ,$this->result['templateRes']['regdate']);?></td>
			    </tr>
			    <tr>
			      <th scope="row"></th>
			      <td>Последний визит</td>
			      <td><?=date('F j, D, Y, g:i a' ,$this->result['templateRes']['lastvisit']);?></td>
			    </tr>
			    <tr>
			      <th scope="row"></th>
			      <td>Привелегии установленными для пользователя</td>
			      <td>N/A</td>
			    </tr>
			    <tr>
			      <th scope="row"></th>
			      <td>Cоциальные данные</td>
			      <td>N/A</td>
			    </tr>
			    <tfooter>
			      <th></th>
			      <td>О себе</td>
			      <td>N/A</td>
			    </tfooter>
			  </tbody>
			</table>
		<br />
		<hr />
		<p>Удалить профиль нажать <a href="<?=$this->allRoutes['/admin/profile/remove']['url'];?>">сюда!</a></p>
	</main>

<?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);



