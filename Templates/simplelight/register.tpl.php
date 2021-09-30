<div id="content">
  <h1>Форма регистрации пользователя!</h1>
  <p>Пример формы регистарции</p>
  <form action="/register" method="post">
    <div class="form_settings">
      <p>
        <span>Имя пользователя</span>
        <input class="contact" type="text" name="username" value="maks_<?=rand(1,10000); ?>" />
      </p>
      <p>
        <span>Емайл пользователя</span>
        <input class="contact" type="text" name="useremail" value="jcmax-<?=rand(1,10000); ?>@gmail.com" />
      </p>
      <p>
        <span>Пароль пользователя</span><input class="contact" type="text" name="userpassword1" value="Hesoyam12" />
      </p>
      <p>
        <span>Повторить пароль</span><input class="contact" type="text" name="userpassword2" value="Hesoyam12" />
      </p>
      <p style="padding-top: 15px">
        <span>&nbsp;</span>
        <input class="submit" type="submit" name="Registration" value="Зарегестрироваться" />
      </p>
    </div>
  </form>
  <p>
    <a href="/login">Войти</a> | <a href="/restore">Забыл пароль</a>
  </p>
</div>
</div>
