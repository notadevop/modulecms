<?php

defined('ROOTPATH') or die();

/**
 * Баннер шаблона
 */

if ($this->regOk){
  return require_once ($this->activeTpl.$r['pages']['default']);
}

require_once($this->activeTpl.$r['templates']['header']);
//require_once($this->activeTpl.$r['templates']['banner']);
?>


<section id="pageContent">
    <main role="main">
        <h1><?=FORMLOGINTITLE;?></h1>
            <form action="<?=$this->allRoutes['/login']['url'];?>" method="post">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label"><?=FORMEMAILTITLE;?></label>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="<?=Identificator::USERMAILVALUE;?>" value="jcmaxuser@gmail.com">
                <div id="emailHelp" class="form-text"><?=FORMEMAILDESC;?></div>
              </div>
              <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label"><?=FORMPWDTITLE;?></label>
                <input type="password" class="form-control" id="exampleInputPassword1" name="<?=Identificator::USERPWD1VALUE;?>" value="Hesoyam12">
              </div>
              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="<?=Identificator::USERALIEN;?>" id="exampleCheck1" 
                value="" />
                <label class="form-check-label" for="exampleCheck1"><?=FORMALIENPCTITLE;?></label>
              </div>

              <input type="hidden" class="id" name="<?=Identificator::CSRFVALUE;?>" value="<?=Csrf::getInputToken(Identificator::CSRFKEY);?>" />

              <input type="submit" class="btn btn-primary" name="loginaction" value="Войти" />
            </form>
            <p class="lead">
                <a href="<?=$this->allRoutes['/restore']['url'];?>">
                  <?=$this->allRoutes['/restore']['urltitle'];?>
                </a> | 
                <a href="<?=$this->allRoutes['/register']['url'];?>">
                  <?=$this->allRoutes['/register']['urltitle'];?>
                </a>
            </p>
    </main>

<?php 

//require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);