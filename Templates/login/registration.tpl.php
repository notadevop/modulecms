<?php

defined('ROOTPATH') or die();


if ($this->regOk){
  return require_once ($this->activeTpl.$r['pages']['default']);
}

require_once($this->activeTpl.$r['templates']['header']);

$tester = strtolower(generateRandomString(rand(4,5)));

$password = '12345678';


// Получить _POST данные и ввести их обратно в форму при ошибке !!!!


?>

<!-- end for header -->


<div class="row justify-content-center">
	<div class="col-md-7 col-lg-5">
		<div class="login-wrap p-4 p-md-5">
	      	<div class="icon d-flex align-items-center justify-content-center"></div>
  			<h3 class="text-center mb-4"><?=FORMREGTITLE;?></h3>

			<form action="<?=$this->allRoutes['/register']['url'];?>" method="post" class="login-form">

					<div class="form-group d-flex">
	          <input type="text" name="<?=Identificator::USERNAMEVALUE;?>" value="<?=$tester;?>" class="form-control rounded-left" placeholder="<?=FORMPWDTITLE;?>" required>
	        </div>

	        <div class="form-group d-flex">
	        	<?=FORMEMAILDESC;?>
	        </div>

		  		<div class="form-group">
		  			<input type="email" name="<?=Identificator::USERMAILVALUE;?>" value="<?=$tester;?>@test.com" class="form-control rounded-left" placeholder="<?=FORMEMAILTITLE;?>" required>
		  		</div>


	        <div class="form-group d-flex">
	          <input type="password" name="<?=Identificator::USERPWD1VALUE;?>" value="<?=$password;?>" class="form-control rounded-left" placeholder="<?=FORMPWDTITLE;?>" required>
	        </div>



	       	<div class="form-group d-flex">
	       		<label for="exampleInputPassword1" class="form-label"></label>
	          <input type="password" name="<?=Identificator::USERPWD2VALUE;?>" value="<?=$password;?>" class="form-control rounded-left" placeholder="<?=FORMPWDTITLEREP;?>" required>
	        </div>

	        <div class="form-group">
	        	<input class="form-control btn btn-primary rounded submit px-3" type="submit" name="<?=Identificator::REGISBKEY;?>" value="<?=FORMREGBUTTON;?>">
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
		      			<a href="<?=$this->allRoutes['/restore']['url'];?>">
		      				<?=$this->allRoutes['/restore']['urltitle'];?></a> 
		     		</label>
		        </div>

		  </form>
		</div>
	</div>
</div>


<!-- end for body -->

<?php 
require_once($this->activeTpl.$r['templates']['footer']);