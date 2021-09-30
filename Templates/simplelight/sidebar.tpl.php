<?php 

  if($regOk) {

    $name   = '<a href="/profile/'.PROFILE['userid'].'">'.PROFILE['username'].'</a>';
    $logout = '<a href="/logout">Выйти?</a>';
    $online = '<a href="/usersonline">('.$result['permanetCtrlResult']['online']['result'].')</a>';
    $notifs = 'Уведомления: <a href="/notifications">(0)</a>';
  } else {
    $name = 'Гость';
    $logout = '<a href="/login">Авторизоваться</a>';
    $online = '('.$result['permanetCtrlResult']['online']['result'].')';
    $notifs = '';
  }

  // Тут временные ссылки для 
  $tempLinkMap = array(

        '/users'        => 'Список пользователей',
        '/priveleges'   => 'Редактирование Привелегий',
        '/notifications'=> 'Центр уведомлений'
  );
?>

<div id="site_content">
  <div class="sidebar">

    <h1>Поиск по сайту</h1>
    <form method="get" action="" id="search_form">
      <p>
        <input class="search" type="text" name="search" value="" placeholder="Поиск по сайту" />
      </p>
    </form>

    <p>Вы вошли на сайт как:(<?=$name; ?>), <?=$logout;?></p>
    <p>Пользователей онлайн: <?=$online;?> <br /> <?=$notifs;?></p>


    <?php  

$tempLinkMap = array(

      'Профиль'     => array(
          '/viewprofile'  => 'Мой профиль',
          '/editpass'     => 'Редактировать Профиль',
          '/editpass'     => 'Изменить пароль',
          '#'             => 'Активные сесии',
      ),
      'Пользователи'    => array(
          '/users'      => 'Список пользователей',
          '/editusers'  => 'Редактировать привелегии',

      ),
      'Страницы'      => array(
          '/addpage'    => 'Добавить страницу',
          '/listpages'  => 'Список страниц',
      ),
      'Настройки'   => array(
          '/settingsgeneral'=> 'Общие',
          '/settingsauth'   => 'Авторизация',
      )
);

?>

<!--================ DYNAMIC DRIVE SOURCE ===============--->
     <div class="arrowlistmenu">
          <?php 
            foreach ($tempLinkMap as $k1 => $v1) {
                
                echo ' <h3 class="menuheader expandable">'.$k1.'</h3>
                        <ul class="categoryitems">';

                foreach ($v1 as $k2 => $v2) {
                   
                  echo '<li><a href="'.$k2.'">'.$v2.'</a></li>';
                }

                echo '</ul>';
            }
          ?>
      </div>
<!--================ DYNAMIC DRIVE SOURCE ===============--->



    <!--

    <h1>Категории</h1>
    <ul>
      <?php
      // тут использовать луп for 
      foreach ($tempLinkMap as $key => $value) {
        
        echo '<li><a href="'.$key.'">'.$value.'</a></li>';
      }
      ?>

    </ul>

    -->

<!--  html code for widget -->

      <h1>Последние новости</h1>
      <h4>New Website Launched</h4>
      <h5>January 1st, 2010</h5>
      <p>2010 sees the redesign of our website. Take a look around and let us know what you think.<br />
      <a href="#">Read more</a></p>

<!--  html code for widget -->
   
  </div>
