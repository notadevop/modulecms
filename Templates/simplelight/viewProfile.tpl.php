
<?php 

if(empty($result['templateCtrlResult']['result'])) { 

  echo '<p> данные не указанны </p>';
  return ; 
}

?>



<div id="content">

<p><a href="/profile/edit/<?=@$result['templateCtrlResult']['result']['id']; ?>">Редактировать профиль</a>  | <a href="">Активные сессии</a></p>

<h1>Профиль пользователя</h1>


<table style="width:100%" >
  <tr>
    <th colspan="2">Данные пользователя</th> 
  </tr>
  <tr>
    <td>Постоянный пользователь</td>
    <?php 
    if ($result['templateCtrlResult']['result']['actstatus'] == 1) {
      echo '<td style="background-color: #c4f9a6;">Активирован</td>';
    } else {
      echo '<td style="background-color: #f9b5a6;">Не Активирован</td>';
    }
    ?>
  </tr>
  <tr>
    <td>Аватар пользователя:</td>
    <td><?=@$result['templateCtrlResult']['result']['userpicture'];?></td>
  </tr>

  <tr>
    <td>Имя пользователя:</td>
    <td><?=@$result['templateCtrlResult']['result']['name'];?></td>
  </tr>
  
  <tr>
    <td>Емайл пользователя:</td>
    <td><?=@$result['templateCtrlResult']['result']['email'];?></td>
  </tr>
  
  <tr>
    <td>Дата Регистрации:</td>
    <td><?=@date('Y M D H:m' ,$result['templateCtrlResult']['result']['regdate']);?></td>
  </tr>

  <tr>
    <td>Последний визит:</td>
    <td><?=@date('Y M D H:m' ,$result['templateCtrlResult']['result']['lastvisit']);?></td>
  </tr>

  <tr>
    <td>Привелегии пользователя:</td>
    <td>(%priveleges%)</td>
  </tr>

  <tr>
    <td>Пароль пользователя:</td>
    <td><b>Указанный пользователем(!)</b></td>
  </tr>

  <tr>
    <td>Соц. профили:</td>
    <td>(%soc.profiles%)</td>
  </tr>

  <tr>
    <td>Об Авторе:</td>
    <td>(%aboutuser%)</td>
  </tr>
</table>
	
</div>
</div>