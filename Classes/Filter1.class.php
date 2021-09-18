<?php 

/**
 * 
 */
class Filter {

	function __construct() { 

		$this->collection 	= array();
		$this->err 			= array();
		$this->fOpt 		= array(); 
	}

	function __destruct() { }

	private $err;
	private $collection;
	private $fOpt;

	// Устанавливаем переменные 

	public function getFilterErrors(): ?array {

		return count($this->err) > 0 ? $this->err : null;
	}

	// Устанавливаем сразу массив переменных и по ним фильтруем каждый как надо

	public function setVariables(array $string): void {

		// Включает в себя ключ с массивом его свойств для фильтрации 
		$this->collection = $string;
	}

	// Удаляем переменные 

	public function eraseKey(string $key): bool {

		if ($this->keyExist($key)){

			unset($this->collection[$key]);
			return true;
		}
		return false;
	}

	// Забираем переменные 

	public function getKey(string $key): ?string {

		return $this->keyExist($key) ? $this->collection[$key]['value'] : null;
	}

	public function keyExist(string $key): bool {

		return !isset($this->collection[$key]) ? false : true;
	}

	// Проверяем существует ли переменная или нет в массиве 

	public function isNotEmpty(string $key): bool {

		return (!isset($this->collection[$key]) || empty($this->collection[$key])) ? false : true;
	}

	// Проверяем меньше, чем указанно

	public function isNotLess(string $key, int $minnumber = 0): bool {

		if (!$this->keyExist($key)) { return false; }

		// Устанавливает новое значение, если из параметра метода, если оно больше 0

		$minNum = $minnumber > 0 ? $minnumber : $this->collection[$key]['minimum'];

		return strlen($this->collection[$key]['value']) < $minNum ? false : true;
	} 

	// Проверяем больше, чем указанно

	public function isNotMore(string $key, int $maxnumber = 0): bool {

		if (!$this->keyExist($key)) return false;

		$maxNum = $this->collection[$key]['maximum'];

		// Эта строка нужна только, если хотите указать сами какое кол-во нужно

		if ($maxnumber > 0) { $maxNum = $maxnumber; }

		return strlen($this->collection[$key]['value']) > $maxNum ? false : true ;
	}

	public function convToNum(string $key): bool {

		if (!$this->keyExist($key)) return false; 

		$value = $this->collection[$key]['value'];

		$this->collection[$key]['value'] = intval($value);

		return true;
	}

	// Обрезаем стринговое значение 
	
	public function cutString(stirng $key): void {

		if (!$this->keyExist($key)) return;

		$string = $this->collection[$key]['value'];

		if ($this->isNotMore($key, $string)) {

			$string = substr($string, 0, $this->collection[$key]['maximum']);
		}

		$this->collection[$key]['value'] = $string;
	}


	function urlValidation(string $input) {

		// ЧТО ОН ВОЗВРАЩАЕТ ??????

		return preg_match('|^(http(s)?://)?[a-z0-9-]+\.(.[a-z0-9-]+)+(:[0-9]+)?(/.*)?$|i', $input);
	}
	
	public function validator(string $key, string $category): bool{

		if (!$this->keyExist($key)) { return false; }

		$input = $this->collection[$key]['value'];

		switch($category) {

			case'boolean': 	$validator = FILTER_VALIDATE_BOOLEAN; 	break;
			case'email': 	$validator = FILTER_VALIDATE_EMAIL; 	break;
			case'float': 	$validator = FILTER_VALIDATE_FLOAT; 	break;
			case'int': 		$validator = FILTER_VALIDATE_INT; 		break;
			case'ip': 		$validator = FILTER_VALIDATE_IP; 		break;
			case'regexp': 	$validator = FILTER_VALIDATE_REGEXP; 	break;
			case'url': 		$validator = FILTER_VALIDATE_URL; 		break;
			default: 		return false;							break;
		}

		return !filter_var($input, $validator) ? false : true ;
	}

	// Очищает 

	public function keepParams(string $key, string $category) {

		// Очистить все кроме букв
		// $str = preg_replace('/[^a-zA-Z]/', '', $input);
		// Очистить все кроме букв и цифр
		// $str = preg_replace('/[^a-zA-Z0-9\s]/', '', $mixed);
	}

	public function sanitizer(string $key, string $category): void {

		if (!$this->keyExist($key)) { return; }

		switch($category) {

			case'email': 			$sanitizer = FILTER_SANITIZE_EMAIL; 			break;
			case'encoding': 		$sanitizer = FILTER_SANITIZE_ENCODED; 			break;
			case'magicquotes': 		$sanitizer = FILTER_SANITIZE_MAGIC_QUOTES; 		break;
			case'float': 			$sanitizer = FILTER_SANITIZE_NUMBER_FLOAT; 		break;
			case'int': 				$sanitizer = FILTER_SANITIZE_NUMBER_INT; 		break;
			case'specchars': 		$sanitizer = FILTER_SANITIZE_SPECIAL_CHARS; 	break;
			case'fullspecchars': 	$sanitizer = FILTER_SANITIZE_FULL_SPECIAL_CHARS;break;
			case'string': 			$sanitizer = FILTER_SANITIZE_STRING; 			break;
			case'stripped': 		$sanitizer = FILTER_SANITIZE_STRIPPED; 			break;
			case'url': 				$sanitizer = FILTER_SANITIZE_URL; 				break;
			default:  				return;											break;
		}

		$this->collection[$key]['value'] = filter_var($this->collection[$key]['value'], $sanitizer);
	}

	 // TODO: Метод временный удалить потом

	// Отфильтровываем все спецсимволы
	// antisql, antixss, antirfi, antilfi, antishell
	public function cleanAttack(string $key, array $filterParams = array()): void {

		if (!$this->keyExist($key)) { return; }

		if(empty($filterParams)) { 

			$filterParams = array('antisql', 'antixss', 'antirfi', 'antilfi', 'antishell');
		}

		// AntiXSS 
		$antixss[] = "/script/i";
		$antixss[] = "/</i";
		$antixss[] = "/>/i";
		$antixss[] = "/alert/i";
		$antixss[] = "/img/i";
		$antixss[] = "/cookie/i";
		$antixss[] = "/href/i";
		$antixss[] = "/input/i";
		$antixss[] = "/form/i";

		// AntiSQL
		$antisql[] = "/--/i";
		$antisql[] = "/;/i";
		$antisql[] = "/'/i";
		$antisql[] = '/"/i';
		$antisql[] = "/0x/i";
		$antisql[] = "/@@/i";
		$antisql[] = "/alter/i";
		$antisql[] = "/char/i";
		$antisql[] = "/begin/i";
		$antisql[] = "/cast/i";
		$antisql[] = "/create/i";
		$antisql[] = "/cursor/i";
		$antisql[] = "/declare/i";
		$antisql[] = "/delete/i";
		$antisql[] = "/drop/i";
		$antisql[] = "/end/i";
		$antisql[] = "/fetch/i";
		$antisql[] = "/insert/i";
		$antisql[] = "/kill/i";
		$antisql[] = "/open/i";
		$antisql[] = "/select/i";
		$antisql[] = "/sys/i";
		$antisql[] = "/update/i";
		$antisql[] = "/union/i";
		$antisql[] = "/or/i";
		$antisql[] = "/from/i";
		$antisql[] = "/like/i";
		$antisql[] = "/and/i";
		$antisql[] = "/all/i";
		$antisql[] = "/group_concat/i";
		$antisql[] = "/order/i";
		$antisql[] = "/version/i";
		$antisql[] = "/by/i";
		$antisql[] = "/table/i";
		$antisql[] = "/database/i";

		// AntiRFI and outgoing scripts like .php .js etc..
		$antirfi[] = "/http/i";
		$antirfi[] = "/.php/i";
		$antirfi[] = "/.js/i";
		$antirfi[] = "/.asp/i";
		$antirfi[] = "/phtm/i";

		// AntiLFI 
		$antilfi[] = "/etc/i";
		$antilfi[] = "/passwd/i";
		$antilfi[] = "/proc/i";
		$antilfi[] = "/self/i";
		$antilfi[] = "/environ/i";

		// AntiSHELL 
		$antishell[] = "/passthru/i";
		$antishell[] = "/exec/i";
		$antishell[] = "/shell/i";
		$antishell[] = "/open/i";
		$antishell[] = "/load_file/i";
		$antishell[] = "/system/i";
		$antishell[] = "/show_source/i";

		$input = $this->collection[$key]['value'];

		$filterAgent = function (array $patterns, string $input) {

			return preg_replace($patterns, '', $input);
		};

		foreach ($filterParams as $k => $v) {
			
			switch($v) {
				case 'antixss': 	$input = $filterAgent($antixss, $input); break;
				case 'antisql': 	$input = $filterAgent($antisql, $input); break;
				case 'antirfi': 	$input = $filterAgent($antirfi, $input); break;
				case 'antilfi': 	$input = $filterAgent($antilfi, $input); break;
				case 'antishell': 	$input = $filterAgent($antishell, $input); break;
			}
		}

		$this->collection[$key]['value'] = $input;
	}


	/*
		$key 		- ключ по которому фильтруються значения
		$options 	- это массим значений по которым фильтруються и проверяються данные
					
		itsMail = true
		itsEmpty = true
		itsUrl = true
		itsMore = true
		cutIt = true; 
		sanitaze => number,html,url,email, etc
		itsLess = true
		itsEmpty = true 
		convertHtml = true // запускает специальный метод для фильтрации html
		cleanHack = true
		getNumber = true
					
	*/

	function letsFilterIt(string $key): void{

		// фильтруем данные например от аттак 

		if ($this->collection[$key]['itsEmpty'] && !$this->isNotEmpty($key)) {

			$this->err[] = 'У вас есть пустые поля';
		}

		if ($this->collection[$key]['itsMore'] && !$this->isNotMore($key)) {

			$this->err[] = 'Разрешенно максимум: '.$this->collection[$key]['maximum'];
		}

		if ($this->collection[$key]['itsLess'] && !$this->isNotLess($key)) {

			$this->err[] = 'Разрешенно минимум: '.$this->collection[$key]['minimum'];
		}

		if ($this->collection[$key]['sanitazer']) {

			$sanParams = $this->collection[$key]['sanitazer'];

			foreach ($sanParams as $key => $value) {
				
				$this->sanitizer($key, $value);
			}
		}

		if (!empty($this->collection[$key]['cleanHack'])) {

			$this->cleanAttack($key);
		}

		if (!empty($this->collection[$key]['itsMail']) && !$this->validator($key, 'email')) {

			$this->err[] = 'Ошибка! Указан некорректный емайл';
		} 

		if (!empty($this->collection[$key]['getNumber']) && !$this->convToNum($key)) {

			$this->err[] = 'Ошибка! Значение нужно указать цифровым';
		}
	}
}











