<?php 


require_once($this->activeTpl.$r['templates']['header']);
//require_once($this->activeTpl.$r['templates']['banner']);
?>

  <section id="pageContent">
    <main role="main">
      <h2>Данная страница отсутствует!</h2>
      <p>Возможные причины</p>
      <ul>
          <li>Битая ссылка</li>
          <li>Cтраница была удалена или не существовала</li>
          <li>Возможно иная проблема</li>
      </ul>
    </main>
<?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);