<?php
/**
 *
 * Баннер шаблона
 */

if ($this->regOk){

  require_once ($this->activeTpl.$r['pages']['default']);
  return;
}


require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);

?>

<section id="pageContent">
    <main role="main">
            <form action="/register" method="post">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label"><?=FORMNAMETITLE;?></label>
                <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="username" value="maks_<?=rand(1,10000); ?>">
              </div>

              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label"><?=FORMEMAILTITLE;?></label>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="useremail" value="jcmaxuser-<?=rand(1,10000); ?>@gmail.com">
                <div id="emailHelp" class="form-text"><?=FORMEMAILDESC;?></div>
              </div>
              <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label"><?=FORMPWDTITLE;?></label>
                <input type="password" class="form-control" id="exampleInputPassword1" name="userpassword1" value="Hesoyam12">
              </div>
              <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label"><?=FORMPWDTITLEREP;?></label>
                <input type="password" class="form-control" id="exampleInputPassword1" name="userpassword2" value="Hesoyam12">
              </div>
              <input type="submit" class="btn btn-primary" name="Registration" value="Зарегестрироваться" />
            </form>
            <p class="lead">
              <a href="<?=$this->allRoutes['/login']['url'];?>">
                  <?=$this->allRoutes['/login']['urltitle'];?>
              </a>  | 
              <a href="<?=$this->allRoutes['/restore']['url'];?>">
                  <?=$this->allRoutes['/restore']['urltitle'];?>
                </a>   
            </p>
    </main>

<?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);
