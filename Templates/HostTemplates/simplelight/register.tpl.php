<?php 

$random = function() {

  return rand(1,10000);
};

?>

<div id="content">
  <h1>Форма регистрации пользователя!</h1>
  <p>Пример формы регистарции</p>
  <form action="/register" method="post">
    <div class="form_settings">
      <p><span>Имя пользователя</span><input class="contact" type="text" name="userregname" value="maksim_<?=$random(); ?>" /></p>
      <p><span>Емайл пользователя</span><input class="contact" type="text" name="userregemail" value="jcmax<?=$random(); ?>@gmail.com" /></p>
      <p><span>Пароль пользователя</span><input class="contact" type="text" name="userregpassword1" value="Hesoyam12" /></p>
      <p><span>Повторить пароль</span><input class="contact" type="text" name="userregpassword2" value="Hesoyam12" /></p>
      <p style="padding-top: 15px"><span>&nbsp;</span><input class="submit" type="submit" name="Registration" value="Зарегестрироваться" /></p>
    </div>
  </form>
  <p><a href="/login">Войти</a> | <a href="/restore">Забыл пароль</a></p>
  <p><br /><br />NOTE: A contact form such as this would require some way of emailing the input to an email address.</p>
</div>
</div>
