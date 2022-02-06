<?php

defined('ROOTPATH') or die();

/**
 * Баннер шаблона
 */



require_once($this->activeTpl.$r['templates']['header']);
?>

			<!-- end for header -->

			
			<div class="row justify-content-center">
				<div class="col-md-7 col-lg-5">
					<div class="login-wrap p-4 p-md-5">
				      	<div class="icon d-flex align-items-center justify-content-center">
				      		<span class="fa fa-user-o"></span>
				      	</div>
		      			<h3 class="text-center mb-4">Страница по умолчанию</h3>

		      			<p>Перейти на главную можно тут: <a href="<?=$this->allRoutes['/']['url'];?>">
                  				<?=$this->allRoutes['/']['urltitle'];?></a>
	        </div>
				</div>
			</div>
			

			<!-- end for body -->

<?php 

//require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);
