<?php 

  if($regOk) {

    $name   = '<a href="/profile/'.PROFILE['userid'].'">'.PROFILE['username'].'</a>';
    $logout = '<a href="/logout">Выйти?</a>';
    $online = '<a href="/usersonline">('.$result['permanetCtrlResult']['online']['result'].')</a>';
  } else {
    $name = 'Гость';
    $logout = '<a href="/login">Авторизоваться</a>';
    $online = '('.$result['permanetCtrlResult']['online']['result'].')';
  }

  // Тут временные ссылки для 
  $tempLinkMap = array(

        '/users' => 'Список пользователей'
  );
?>

<div id="site_content">
  <div class="sidebar">

    <h1>Search</h1>
    <form method="get" action="" id="search_form">
      <p>
        <input class="search" type="text" name="search" value="" placeholder="Поиск по сайту" />
        <!--<input name="search" type="submit" value="Поиск" alt="Search" title="Search" />-->
      </p>
    </form>

    <p>Вы вошли на сайт как:(<?=$name; ?>), <?=$logout;?></p>
    <p>Пользователей онлайн: <?=$online;?></p>

    <h1>Полезные ссылки</h1>
    <ul>
      <?php
          // тут использовать луп for 

      foreach ($tempLinkMap as $key => $value) {
        
        echo '<li><a href="'.$key.'">'.$value.'</a></li>';
      }

      ?>

    </ul>

    <h1>Последние новости</h1>
    <h4>New Website Launched</h4>
    <h5>January 1st, 2010</h5>
    <p>2010 sees the redesign of our website. Take a look around and let us know what you think.<br />
      <a href="#">Read more</a></p>



  </div>
