<?php 

defined('ROOTPATH') or die();

/**
 *
 * 
 */

echo '<pre>';
print_r($this->result);
echo '</pre>';


$website_title 	= $this->result['templateRes']['website_title'];
$website_desc 	= $this->result['templateRes']['website_title_description'];
$admin 					= $this->result['templateRes']['admin'];



require_once($this->activeTpl.$r['templates']['header']);
require_once($this->activeTpl.$r['templates']['banner']);



?>

  <section id="pageContent">
		<main role="main">

			<h2>Настройки вебсайта</h2>

				<br /><hr />

				<form class="row g-3 needs-validation" novalidate action="" method="POST">
					<div class="input-group input-group-lg">
					  <span class="input-group-text" id="inputGroup-sizing-lg">Заголовок вебсайта</span>
					  <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg" name="websiteTitle" value="<?=$website_title;?>" required>
					</div>
					
					<div class="input-group mb-3">
					  <span class="input-group-text" id="inputGroup-sizing-default">Пояснение</span>
					  <input type="text" class="form-control" name="websiteDesc" value="<?=$website_desc;?>" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" require>
					</div>

					<div class="col-auto">
					  <button type="submit" class="btn btn-primary mb-3">Обновить</button>
					</div>
				</form>


			<br /><hr />

			<label for="validationDefault04" class="form-label">Чтобы отключить хостинг для посещения установите значение тут</label>
			<br />
			<label class="form-label">Хост (<b>Включен</b>)</label>
			<form class="row g-3" action="" method="POST">
			  <div class="col-auto">

			    <select name='hostEnabled' class="form-select" id="validationDefault04" required>

			    	<?php 

			    	for ($i = 1; $i <= 2; $i++) {
			    			
			    		//echo '<option value="" '; 
			    		//if () { echo 'selected'; }
			    		//echo '>Отключить хост</option>';
			    	}

			    	?>

			      <option selected value='enabled'>Включить хост</option>
			      <option value='disabled'>Отключить хост</option>
			    </select>
			  </div>
			  <div class="col-auto">
			    <button type="submit" class="btn btn-primary mb-3">Установить</button>
			  </div>
			</form>

			<br /><hr />

			<form class="row g-6" action="" method="POST">
				<label for="validationDefault04" class="form-label">Админ часть доступна по адресу: (<a href="<?='http://'.HOST.DS.$admin;?>"><?=HOST.DS;?><b><?=$admin;?></b></a>)</label>
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon3"><?=HOST.DS;?></span>
				  <input type="text" class="form-control" placeholder="Имя административной части" aria-label="Recipient's username" aria-describedby="button-addon2" 
				  name="adminpage" value="<?=$admin;?>">
				  <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Обновить</button>
				</div>
			</form>

			<br />

			<form class="row g-6" action="" method="POST">
				<label for="validationDefault04" class="form-label">Авторизация доступна по адресу: (<?=HOST.DS;?><b>Login</b>)</label>
				<div class="input-group mb-3">
					<span class="input-group-text" id="basic-addon3"><?=HOST.DS;?></span>
				  <input type="text" class="form-control" placeholder="Имя страницы авторизации" aria-label="" aria-describedby="button-addon2">
				  <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Обновить</button>
				</div>
			</form>

			<br /><hr />

			<label class="form-label">Включение/Отключение страниц авторизации!</label>

				<form class="row g-3" action="" method="POST">
					<label class="form-label">Авторизация (<b>Включена</b>)</label>

					<select class="form-select form-select-sm" aria-label=".form-select-sm example">
					  <option selected>Включить авторизацию</option>
					  <option value="1">Отключить авторизацию</option>
					</select>

					<label class="form-label">Регистрация (<b>Включена</b>)</label>
						
					<select class="form-select form-select-sm" aria-label=".form-select-sm example">
					  <option selected value="">Включить регистрацию</option>
					  <option value="1">Отключить регистрацию</option>
					</select>

					<label class="form-label">Аутентификация (<b>Включена</b>)</label>

					<select class="form-select form-select-sm" aria-label=".form-select-sm example">
					  <option selected value="">Включить аутентификацию</option>
					  <option value="1">Отключить аутентификацию</option>
					</select>

				  <div class="col-auto">
				    <button type="submit" class="btn btn-primary mb-3">Установить</button>
				  </div>
				</form>

				<br /><hr />

				<form class="row g-3" action="" method="POST">

					<label for="validationDefault04" class="form-label">Cписок шаблонов</label>
					<label for="validationDefault04" class="form-label">Активный шаблон (<b>Bootstrap</b>)</label>
					<select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
					  <option selected>Bootstrap</option>
					  <option value="1">Другой шаблон</option>
					  <option value="2">Шаблон simplelight</option>
					  <option value="3">Не готовый шаблон</option>
					</select>

					<div class="col-auto">
					    <button type="submit" class="btn btn-primary mb-3">Установить</button>
					</div>
				</form>

				<br /><hr />


			<p> Время сессии пользователей </p>

			<pre>

1. Время которое пользователь может находиться на хосте без действия 
2. Время заданное для ожидания активации только зарегестрированного пользователя
3. Время для ожидания активации восстановления пароля
4. Время токен CSRF или тому подобных

			</pre>


		</main>

<?php 

require_once($this->activeTpl.$r['templates']['sidebar']);
require_once($this->activeTpl.$r['templates']['footer']);