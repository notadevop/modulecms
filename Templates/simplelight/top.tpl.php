<?php 
$toplinks = array(

    '/'               => 'Главная'
);
?>
<div id="header">
  <div id="logo">
    <h1><span class="logo_colour"><a href="/"> %sitetitle% </span></a></h1>
    <h2> %website_description% </h2>
  </div>
  <div id="menubar">
    <ul id="menu">

      <?php 
      foreach ($toplinks as $key => $value) {
        echo (!empty($curRoutePath['uri']) && $curRoutePath['uri'] == $key) ? '<li class="selected">' : '<li>';
        echo '<a href="'.$key.'">'.$value.'</a></li>';
      }
      ?>
    </ul>   
  </div>
</div>


