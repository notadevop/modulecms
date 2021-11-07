<?php 
/**
 * 
 */

if(!$this->regOk || empty($this->result['templateRes'])) { 

	require_once($this->activeTpl.$r['templates']['header']);
	//require_once($this->activeTpl.$r['templates']['banner']);
	?>
	<section id="pageContent">
	    <main role="main"></main>

	<?php 
	//require_once($this->activeTpl.$r['templates']['sidebar']);
	require_once($this->activeTpl.$r['templates']['footer']);
	return; 
}

require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);
?>

<section id="pageContent">
    <main role="main">
	    	<h2>Профиль </h2>
	    	<p><?=@$this->result['templateRes']['userpicture'];?></p>
	    	<table class="table">
			  <thead>
			    <tr>
			      <th scope="col"></th>
			      <th scope="col">Имя пользователя</th>
			      <th scope="col"><?=@$this->result['templateRes']['name'];?></th>
			    </tr>
			  </thead>
			  <tbody>

			  	<?php 
			  	if ($this->result['templateRes']['actstatus'] == 1) {
			  		echo '<tr class="table-success">';
			  		$stat = 'Активирован!';
			  	} else {
			  		echo '<tr class="table-warning">';
			  		$stat = 'Не Активирован!';
			  	}

			  	?>
			      <th scope="row"></th>
			      <td>Cтатус пользователя</td>
			      <td><?=$stat;?></td>
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
			      <td>Привелегии не установленны!</td>
			    </tr>
			    <tr>
			      <th scope="row"></th>
			      <td>Cоциальные данные</td>
			      <td>Данных нету</td>
			    </tr>
			    <tr>
			      <th scope="row"></th>
			      <td>О себе</td>
			      <td>Данных нету</td>
			    </tr>
			  </tbody>
			</table>
	</main>

<?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);



