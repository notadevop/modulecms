<div id="content">

  <p><a href="/profile/<?=@$result['templateCtrlResult']['result']['id']; ?>">Просмотреть профиль</a>  | <a href="">Активные сессии</a></p>

  <h1>Форма редактирования профиля</h1>

  <h1>Персональные данные</h1>
  <form action="/profile/edit/<?=$result['templateCtrlResult']['result']['id']; ?>" method="post">
    
    <img src="../../Templates/simplelight/img/userpicture.jpg" />

    <div class="form_settings">
      <p><span>Имя Пользователя</span>
        <input class="contact" type="text" name="username" value="" placeholder="Имя пользователя" /></p>

      <p><span>Аватар Пользователя</span>
        <input class="contact" type="file" name="userpicture" value="" /></p>

      <p><span>Социальные профили Пользователя</span>
        <input class="contact" type="text" name="userpicture" value="" /></p>
      
      <p><span>О себе</span>
        <textarea class="contact textarea" rows="8" cols="77" name=""></textarea></p>

      <p style="padding-top: 15px"><span>&nbsp;</span><input class="submit" type="submit" name="loginaction" value="Обновить данные" /></p>
    </div>
  </form>

  <h1>Емайл пользователя</h1>
  <form action="/profile/edit/<?=@$result['templateCtrlResult']['result']['id']; ?>" method="post">

    <p><span style="color: red;">Для смены емайла вам нужно его подтвердить по ссылке присланной на новый емайл</span></p>
     <div class="form_settings">
      
      <p><span>Емайл пользователя</span>
        <input class="contact" type="text" name="useremail1" value="" placeholder="Емайл пользователя" /></p>

      <p><span>Емайл пользователя повторить</span>
        <input class="contact" type="text" name="useremail2" value="" placeholder="Емайл пользователя повторить" /></p>

      <p style="padding-top: 15px"><span>&nbsp;</span><input class="submit" type="submit" name="loginaction" value="Обновить Емайл" /></p>
    </div>
  </form>

  <h1>Пароль пользователя</h1>
  <form action="/profile/edit/<?=@$result['templateCtrlResult']['result']['id']; ?>" method="post">
     <div class="form_settings">
      <p><span>Старый Пароль пользователя</span>
        <input class="contact" type="text" name="olduserpassword" value="" placeholder="Cтарый пароль пользователя" /></p>
      <p><span>Новый пароль пользователя</span>
        <input class="contact" type="text" name="userpassword1" value="" placeholder="Новый пароль пользователя" /></p>
      <p><span>Новый пароль пользователя повторить</span>
        <input class="contact" type="text" name="userpassword2" value="" placeholder="Пароль пользователя повторить" /></p>

      <p style="padding-top: 15px"><span>&nbsp;</span><input class="submit" type="submit" name="loginaction" value="Обновить пароль" /></p>
    </div>
  </form>


  <?php 

  	$allowToShow = true;

  	if ($allowToShow) {

  		require_once ($tplFolder . 'priveleges.tpl.php');
  	}
  ?>
</div>
</div>