<div id="content">
  <h1>Новый пароль пользователя</h1>
  <p>Форма обновления пароля пользователя</p>
  <form action="/confpass/?<?=http_build_query($metadata['result']);?>" method="post">
    <div class="form_settings">
      <p><span>Новый пароль</span><input class="contact" type="text" name="newpassword1" value="" placeholder="Новый пароль" /></p>
      <p><span>Повторить пароль</span><input class="contact" type="text" name="newpassword2" value="" placeholder="Повторить пароль" /></p>
      <p style="padding-top: 15px"><span>&nbsp;</span><input class="submit" type="submit" name="Restoreaction" value="Обновить пароль" /></p>
    </div>
  </form>
  <p><br /><br />NOTE: A contact form such as this would require some way of emailing the input to an email address.</p>
</div>
</div>
