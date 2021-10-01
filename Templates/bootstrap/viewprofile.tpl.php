<?php 
/**
 * 
 */

if(empty($this->result['templateCtrlResult']['result'])) { 

	echo '<h1> данные не указанны </h1>';
	return; 
}




?>

<section id="pageContent">
    <main role="main">
	    	<h2>Профиль </h2>
	    	<p><?=@$this->result['templateCtrlResult']['result']['userpicture'];?></p>
	    	<table class="table">
			  <thead>
			    <tr>
			      <th scope="col"></th>
			      <th scope="col">Имя пользователя</th>
			      <th scope="col"><?=@$this->result['templateCtrlResult']['result']['name'];?></th>
			    </tr>
			  </thead>
			  <tbody>
			    <tr>
			      <th scope="row"></th>
			      <td>Статус пользователя</td>  
			      	<?php 
					    if ($this->result['templateCtrlResult']['result']['actstatus'] == 1) {
					      echo '<td class="table-success">Активирован</td>';
					    } else {
					      echo '<td class="table-warning">Не Активирован</td>';
					    }
					?>
			    </tr>
			    <tr>
			      <th scope="row"></th>
			      <td>Пользовательский емайл</td>
			      <td><?=$this->result['templateCtrlResult']['result']['email'];?></td>
			    </tr>
			    <tr>
			      <th scope="row"></th>
			      <td>Дата регистрации</td>
			      <td><?=date('F j, D, Y, g:i a' ,$this->result['templateCtrlResult']['result']['regdate']);?></td>
			    </tr>
			    <tr>
			      <th scope="row"></th>
			      <td>Последний визит</td>
			      <td><?=date('F j, D, Y, g:i a' ,$this->result['templateCtrlResult']['result']['lastvisit']);?></td>
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