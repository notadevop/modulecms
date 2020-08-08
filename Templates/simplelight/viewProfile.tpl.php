
<?php 

if(empty($result['templateCtrlResult']['result']['access'])) { return ; }


?>



<div id="content">

<p><a href="/profile/edit/<?=@PROFILE['userid']; ?>">Редактировать профиль</a>  | <a href="">Активные сессии</a></p>

<h1>Профиль пользователя</h1>


<table style="width:100%" >
  <tr>
    <th colspan="2">Данные пользователя</th> 

  </tr>

  <tr>
    <td>Аватар пользователя:</td>
    <td>(%avatarpicture%)</td>
  </tr>

  <tr>
    <td>Имя пользователя:</td>
    <td><?=@$result['templateCtrlResult']['result']['access']['username'];?></td>
  </tr>
  
  <tr>
    <td>Емайл пользователя:</td>
    <td><?=@$result['templateCtrlResult']['result']['access']['useremail'];?></td>
  </tr>
  
  <tr>
    <td>Дата Регистрации:</td>
    <td><?=@date('Y M D H:m' ,$result['templateCtrlResult']['result']['access']['userregd']);?></td>
  </tr>

  <tr>
    <td>Последний визит:</td>
    <td><?=@date('Y M D H:m' ,$result['templateCtrlResult']['result']['access']['userlastv']);?></td>
  </tr>

  <tr>
    <td>Привелегии пользователя:</td>
    <td>(%priveleges%)</td>
  </tr>

  <tr>
    <td>Пароль пользователя:</td>
    <td><b>***********</b></td>
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