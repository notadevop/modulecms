<?php
/**
 *
 * Баннер шаблона
 */

if ($this->regOk || empty($this->result['templateRes']) ){

  return require_once ($this->activeTpl.$r['pages']['default']);
} 

$query = '';

if (!empty($this->result['templateRes'])) {

  if(!is_bool($this->result['templateRes'])) {

    $query = http_build_query($this->result['templateRes']);
  } else {
    return require_once ($this->activeTpl.$r['pages']['default']);
  }
}

require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);

?>
<section id="pageContent">
    <main role="main">
      <h1><?=FORMPWDTITLE;?></h1>
      <form action="<?=$this->allRoutes['/updatepassword']['url'].'?'.$query;?>" method="post">
        <div class="mb-3">
          <label for="exampleInputPassword1" class="form-label"><?=FORMNEWPWD1;?></label>
          <input type="password" class="form-control" id="exampleInputPassword1" name="userpassword1">
        </div>
        <div class="mb-3">
          <label for="exampleInputPassword1" class="form-label"><?=FORMNEWPWD2;?></label>
          <input type="password" class="form-control" id="exampleInputPassword1" name="userpassword2">
        </div>
        <div class="mb-3">
          <input type="submit" class="btn btn-primary" name="Restoreaction" value="<?=FORMPWDBUTTON;?>" />
        </div>

        <?php //debugger($this->result);  ?>
      
      </form>
    </main>
<?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);
