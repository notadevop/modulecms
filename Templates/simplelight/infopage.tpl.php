<?php

  if($regOk) {
?>
	<ul>
		<li>Вы вошли как: <a href="/profile"><?=PROFILE['username']; ?></a></li> 
    	<li><a href="/logout">Выйти?</a></li>
    	<li><a href="/usersonline">Пользователей онлайн: (<?=$result['permanetCtrlResult']['online']['result']; ?>)</a></li>
	</ul>
  <?php
  } else {
  	?>
  	<ul>
  		<li>Вы вошли как: (Гость)</li>
  		<li> Пользователей онлайн: (<?=$result['permanetCtrlResult']['online']['result']; ?>)</li>
  	</ul>
  	<?php 
  }
?>