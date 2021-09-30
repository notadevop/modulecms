<div id="content">

    <h1>Форма привелегий</h1>

    <form action="" method="post">
      <div class="form_settings">
    <?php 

    // Поже удалить
    $listPriv = array('Администратор' => 'administrator','Модератор' => 'moderator',);

    if(!empty($listPriv)) {
      foreach ($listPriv as $key => $value) {
        ?>
        <p><span><?=$value;?></span>
          <input class="contact" type="checkbox" name="<?=$key;?>" value="" />
        </p>
        <?php
      }
    } else {
      echo '<p> Привелегии в базе не найдены</p>';
    }
    ?>
      <p style="padding-top: 55px"><span>&nbsp;</span>
        <input class="submit" type="submit" name="UpdatePriveleges" value="Обновить привелегии" />
      </p>
    </div>
    </form>
</div>
</div>