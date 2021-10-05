<?php
/**
 *
 * Баннер шаблона
 */

if ($this->regOk){

  require_once ($this->activeTpl.$r['pages']['default']);
  return;
}



if (!empty($this->result['templateCtrlResult']['result'])) {

  $query = http_build_query($this->result['templateCtrlResult']['result']);
}



$action = '/updatePassword/?'.@$query;

require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);

?>


<section id="pageContent">
    <main role="main">
      <h1>Форма восстановления пароля пользователя</h1>
      <form action="<?=$action;?>" method="post">
        <div class="mb-3">
          <label for="exampleInputPassword1" class="form-label">Новый пароль пользователя</label>
          <input type="password" class="form-control" id="exampleInputPassword1" name="userpassword1">
        </div>
        <div class="mb-3">
          <label for="exampleInputPassword1" class="form-label">Новый пароль повторить</label>
          <input type="password" class="form-control" id="exampleInputPassword1" name="userpassword2">
        </div>
        <div class="mb-3">
          <input type="submit" class="btn btn-primary" name="Restoreaction" value="Обновить пароль пользователя" />
        </div>
      </form>
    </main>

<?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);
