<?php
/**
 *
 * Баннер шаблона
 */

?>

<section id="pageContent">
    <main role="main">
     
            <form action="/login" method="post">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email Пользователя</label>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="useremail" value="jcmaxuser@gmail.com">
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
              </div>
              <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Пароль Пользователя</label>
                <input type="password" class="form-control" id="exampleInputPassword1" name="userpassword1" value="Hesoyam12">
              </div>
              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Check me out</label>
              </div>
              <input type="submit" class="btn btn-primary" name="loginaction" value="Войти" />
            </form>
            <p class="lead">
                <a href="/restore">Забыл пароль</a> | <a href="/register">Регистрация</a>
            </p>
    </main>