<?php

defined('ROOTPATH') or die();


if ($this->regOk){
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

?>

<!-- end for header -->


<div class="row justify-content-center">
	<div class="col-md-7 col-lg-5">
		<div class="login-wrap p-4 p-md-5">
	      	<div class="icon d-flex align-items-center justify-content-center"></div>
  			<h3 class="text-center mb-4"><?=FORMPWDTITLE;?></h3>

			<form action="<?=$this->allRoutes['/updatepassword']['url'].'?'.$query;?>" method="post" class="login-form">

	  		<div class="form-group">
	  			<input type="password" name="<?=Identificator::USERPWD1VALUE;?>" class="form-control rounded-left" placeholder="<?=FORMNEWPWD1;?>" required>
	  		</div>

        <div class="form-group d-flex">
          <input type="password" name="<?=Identificator::USERPWD2VALUE;?>" class="form-control rounded-left" placeholder="<?=FORMNEWPWD2;?>" required>
        </div>

        <div class="form-group">
        	<input class="form-control btn btn-primary rounded submit px-3" type="submit" name="<?=Identificator::UPDPWDBKEY;?>" value="<?=FORMPWDBUTTON;?>">
        </div>

        <div class="form-group d-md-flex">
        	<input type="hidden" name="<?=Identificator::CSRFVALUE;?>" value="<?=Csrf::getInputToken(Identificator::CSRFKEY);?>" />
        </div>
		       	
				<div class="form-group d-flex">
	        	<label>
	        		<a href="<?=$this->allRoutes['/']['url'];?>"><?=$this->allRoutes['/']['urltitle'];?></a> |
        			<a href="<?=$this->allRoutes['/login']['url'];?>"><?=$this->allRoutes['/login']['urltitle'];?></a> 
	     		</label>
		    </div>

		  </form>
		</div>
	</div>
</div>


<!-- end for body -->

<?php 
require_once($this->activeTpl.$r['templates']['footer']);
