<?php
/**
 *
 * Баннер шаблона
 */

if ($this->regOk){

  require_once ($this->activeTpl.$r['pages']['default']);
  return;
}


require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);

?>

<section id="pageContent">
    <main role="main">
        <h1>Форма запроса пароля</h1>
        <form action="/restore" method="post">
          <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Email Пользователя</label>
            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="useremail" value="jcmaxuser@gmail.com">
            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
          </div>
          <input type="submit" class="btn btn-primary" name="Restoreaction" value="Отправить запрос" />
        </form>
        <p class="lead">
          <a href="/login">Вход</a> | <a href="/register">Регистрация</a>
        </p>
    </main>

<?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);