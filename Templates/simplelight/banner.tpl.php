<div id="content">

<!-- <h1>Это перманентный баннер!</h1> -->
<?php 



// @params: warnings, attentions, information, success


$alerts = array(

	'warnings'    => array(
		'css_class'		=> 'alert',
		'description' 	=> 'Возникли проблемы указанные ниже:',
	),	
	'attentions'  => array(
		'css_class'		=> 'alert warning',
		'description'  	=> 'Внимание, есть определенные проблемы',
	),
	'information' => array(
		'css_class'		=> 'alert info',
		'description' 	=>'Есть определенная информация:',
	),
	'success'		=> array(
		'css_class' 	=> 'alert success',
		'description' 	=> 'Все ОК!',
	)
);

// TODO: Использовать эти алерты только для контроллеров
// NB!: Системные ошибки использовать в консоли
/*
Logger::collectAlert('warnings', 'Внимание это тестовое сообщение');
*/

foreach ($alerts as $key => $value) {

	if (Logger::alertKeyExist($key)) {
	
		?>
		<div class="<?=$value['css_class']; ?>">
		<span class="closebtn">&times;</span>  
		<strong><?=$value['description']; ?></strong> 
			<ul class="none">
				<?php 
				foreach (Logger::getAlerts($key) as $k => $v) 
					{ echo '<li>'.$v.'</li>'; }
				?>
			</ul>
		</div>
		<?php 
	} 
}

?>

<script>
	var close = document.getElementsByClassName("closebtn");
	var i;
	for (i = 0; i < close.length; i++) {
		close[i].onclick = function() {
			var div = this.parentElement;
			div.style.opacity = "0";
			setTimeout(function(){ div.style.display = "none"; }, 1000);
		}
	}
</script>
</div>

<?php 

// Смарт баннер выводи информации или ссылок 

debugger($result, 'Результат отработаных контроллеров!');

?>
<!--
<div class="alert">
	<span class="closebtn">&times;</span>  
	<strong>Ошибка!</strong> 
	Возможные причины:
	<ul>
		<li>Indicates a dangerous or potentially negative action.</li>
	</ul>
</div>

<div class="alert success">
<span class="closebtn">&times;</span>  
<strong>Success!</strong> 
Indicates a successful or positive action.
</div>

<div class="alert info">
<span class="closebtn">&times;</span>  
<strong>Info!</strong> 
Indicates a neutral informative change or action.
</div>

<div class="alert warning">
<span class="closebtn">&times;</span>  
<strong>Warning!</strong> 
Indicates a warning that might need attention.
</div>
<div>
-->



<!--
<div id="content">
<h1>Welcome to the simple_light template</h1>
<p>This standards compliant, simple, fixed width website template is released as an 'open source' design (under a <a href="http://creativecommons.org/licenses/by/3.0">Creative Commons Attribution 3.0 Licence</a>), which means that you are free to download and use it for anything you want (including modifying and amending it). All I ask is that you leave the 'design from HTML5webtemplates.co.uk' link in the footer of the template, but other than that...</p>
<p>This template is written entirely in <strong>HTML5</strong> and <strong>CSS</strong>, and can be validated using the links in the footer.</p>
<p>You can view more free HTML5 web templates <a href="http://www.html5webtemplates.co.uk">here</a>.</p>
<p>This template is a fully functional 5 page website, with an <a href="examples.html">examples</a> page that gives examples of all the styles available with this design.</p>
<h2>Browser Compatibility</h2>
<p>This template has been tested in the following browsers:</p>
<ul>
<li>Internet Explorer 8</li>
<li>FireFox 3</li>
<li>Google Chrome 13</li>
</ul>
</div>
</div>
-->