<?php 

  if($regOk) {

    $name   = '<a href="/profile">'.PROFILE['username'].'</a>';
    $logout = '<a href="/logout">Выйти?</a>';
    $online = '<a href="/usersonline">('.$result['permContrResult']['online']['result'].')</a>';
  } else {
    $name = 'Гость';
    $logout = '';
    $online = '('.$result['permContrResult']['online']['result'].')';
  }
?>

<div id="site_content">
  <div class="sidebar">

    <p>Вы вошли на сайт как:(<?=$name; ?>), <?=$logout;?>

  Онлайн: <?=$online;?></p>

    <h4> Пользователей онлайн (0) </h4>
    <h4> Вы вошли как: (Гость) </h4>


    <ul>
      <?php
          // тут использовать луп for 
      ?>
      <li><a href="/login">Вход</a></li>
      <li><a href="/register">Регистрация</a></li>
      <li><a href="/restore">Восстановление пароля</a></li>

    </ul>
    <h1>Latest News</h1>
    <h4>New Website Launched</h4>
    <h5>January 1st, 2010</h5>
    <p>2010 sees the redesign of our website. Take a look around and let us know what you think.<br /><a href="#">Read more</a></p>
    <h1>Useful Links</h1>
    <ul>
      <?php
          // тут использовать луп for 
      ?>
      <li><a href="#">link 1</a></li>
      <li><a href="#">link 2</a></li>
      <li><a href="#">link 3</a></li>
      <li><a href="#">link 4</a></li>
    </ul>
    <h1>Search</h1>
    <form method="get" action="" id="search_form">
      <p>
        <input class="search" type="text" name="search_field" value="Enter keywords....." />
        <input name="search" type="image" style="border: 0; margin: 0 0 -9px 5px;" src="Template/simplelight/style/search.png" alt="Search" title="Search" />
      </p>
    </form>
  </div>
