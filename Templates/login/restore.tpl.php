<?php

defined('ROOTPATH') or die();


if ($this->regOk){
  return require_once ($this->activeTpl.$r['pages']['default']);
}

require_once($this->activeTpl.$r['templates']['header']);
?>

<!-- end for header -->


<div class="row justify-content-center">
	<div class="col-md-7 col-lg-5">
		<div class="login-wrap p-4 p-md-5">
    	<div class="icon d-flex align-items-center justify-content-center"></div>
  		<h3 class="text-center mb-4"><?=FORMPWDREQUEST;?></h3>

			<form action="<?=$this->allRoutes['/restore']['url'];?>" method="post" class="login-form">

				<div class="form-group">
					<input type="email" name="<?=Identificator::USERMAILVALUE;?>" value="testuser@test.com" class="form-control rounded-left" placeholder="<?=FORMEMAILTITLE;?>" required>
				</div>

		    <div class="form-group">
		    	<input class="form-control btn btn-primary rounded submit px-3" type="submit" name="<?=Identificator::RESTORBKEY;?>" value="<?=FORMRESBUTTON;?>">
		    </div>

		    <div class="form-group d-md-flex">
		    	<input type="hidden" name="<?=Identificator::CSRFVALUE;?>" value="<?=Csrf::getInputToken(Identificator::CSRFKEY);?>" />
		    </div>
		     	
				<div class="form-group d-flex">
		    	<label>
		    		<a href="<?=$this->allRoutes['/']['url'];?>">
		  				<?=$this->allRoutes['/']['urltitle'];?></a> | 
		  			<a href="<?=$this->allRoutes['/login']['url'];?>">
            	<?=$this->allRoutes['/login']['urltitle'];?></a> | 
		  			<a href="<?=$this->allRoutes['/register']['url'];?>">
		 					<?=$this->allRoutes['/register']['urltitle'];?></a>
		 			</label>
		    </div>

		  </form>
		</div>
	</div>
</div>


<!-- end for body -->

<?php 
require_once($this->activeTpl.$r['templates']['footer']);