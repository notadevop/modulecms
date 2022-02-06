<?php

defined('ROOTPATH') or die();

/**
 * Баннер шаблона
 */

if ($this->regOk){
  return require_once ($this->activeTpl.$r['pages']['default']);
}

require_once($this->activeTpl.$r['templates']['header']);
?>

			<!-- end for header -->


			<div class="row justify-content-center">
				<div class="col-md-7 col-lg-5">
					<div class="login-wrap p-4 p-md-5">
				      	<div class="icon d-flex align-items-center justify-content-center">
				      		<span class="fa fa-user-o"></span>
				      	</div>
		      			<h3 class="text-center mb-4"><?=FORMLOGINTITLE;?></h3>

				<form action="<?=$this->allRoutes['/login']['url'];?>" method="post" class="login-form">

		      		<div class="form-group">
		      			<input type="email" name="<?=Identificator::USERMAILVALUE;?>" value="jcmaxuser@gmail.com" class="form-control rounded-left" placeholder="<?=FORMEMAILTITLE;?>" required>
		      			<div id="emailHelp" class="form-text"><?=FORMEMAILDESC;?></div>
		      		</div>
		            <div class="form-group d-flex">
		              <input type="password" name="<?=Identificator::USERPWD1VALUE;?>" value="Hesoyam12" class="form-control rounded-left" placeholder="<?=FORMPWDTITLE;?>" required>
		            </div>
		            <div class="form-group">
		            	<input class="form-control btn btn-primary rounded submit px-3" type="submit" name="loginaction" value="Войти">
		            </div>


		            <div class="form-group d-md-flex">
		            	<input type="hidden" name="<?=Identificator::CSRFVALUE;?>" value="<?=Csrf::getInputToken(Identificator::CSRFKEY);?>" />
		            </div>
		           	

					<div class="form-check">
					  <input class="form-check-input" type="checkbox" name="<?=Identificator::USERALIEN;?>" value="" id="flexCheckDefault">
					  <label class="form-check-label" for="flexCheckDefault">
					    <?=FORMALIENPCTITLE;?>
					  </label>
					</div>


					<div class="form-group d-flex">
		            	<label><a href="<?=$this->allRoutes['/']['url'];?>">
                  				<?=$this->allRoutes['/']['urltitle'];?></a> | <a href="<?=$this->allRoutes['/restore']['url'];?>">
                  				<?=$this->allRoutes['/restore']['urltitle'];?></a> | <a href="<?=$this->allRoutes['/register']['url'];?>">
                 				<?=$this->allRoutes['/register']['urltitle'];?></a>
                 		</label>
		            </div>

	          </form>
	        </div>
				</div>
			</div>
		

			<!-- end for body -->

<?php 

//require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);
