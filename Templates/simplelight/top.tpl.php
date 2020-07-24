<?php 
$toplinks = array(

    '/'       => 'Главная',
    '/404err' => '404cтраница',
    '/online' => 'Онлайн пользователи',
    '/info'   => 'Чаво'
);
?>
<div id="header">
  <div id="logo">
    <!-- class="logo_colour", allows you to change the colour of the text -->
    <h1><a href="/">simple<span class="logo_colour">_light</span></a></h1>
    <h2>Пояснение к заголовку вебсайта</h2>
  </div>
  <div id="menubar">
    <ul id="menu">
      <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->

      <?php 

      foreach ($toplinks as $key => $value) {
    
        echo $_SERVER['PHP_SELF'] == $key ? '<li class="selected">' : '<li>';
        echo '<a href="'.$key.'">'.$value.'</a></li>';
      }

      ?>

    </ul>   
  </div>
</div>


