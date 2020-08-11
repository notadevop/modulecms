
<?php 

  // Пожже удалить 

  $query = http_build_query($result['templateCtrlResult']['result']);

  $action = '/confpass/?'.$query;
?>



<div id="content">
  <h1>Новый пароль пользователя</h1>
  <p>Форма обновления пароля пользователя</p>
  <form action="<?=$action;?>" method="post">
    <div class="form_settings">
      <p><span>Новый пароль</span><input class="contact" type="text" name="newpassword1" value="" placeholder="Новый пароль" /></p>
      <p><span>Повторить пароль</span><input class="contact" type="text" name="newpassword2" value="" placeholder="Повторить пароль" /></p>
      <p style="padding-top: 15px"><span>&nbsp;</span><input class="submit" type="submit" name="Restoreaction" value="Обновить пароль" /></p>
    </div>
  </form>
  <p><br /><br /></p>
</div>
</div>
