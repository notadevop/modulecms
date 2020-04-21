<?php 

/**
 * 
 */
class Filter {

	function __construct() { 

		$this->strCollector = array();
		$this->err 			= array();
	}

	function __destruct() { }

	private $err;
	private $strCollector;

	// Устанавливаем переменные 

	public function setVariables(array $string): void {

		$this->strCollector = $string;
	}

	// Удаляем переменные 

	public function eraseKey(string $key): bool {

		if ($this->keyExist($key)){

			unset($this->strCollector[$key]);
			return true;
		}
		return false;
	}

	// Забираем переменные 

	public function getKey(string $key): ?string {

		if ($this->keyExist($key)) { return $this->strCollector[$key]['value'];  }

		return null;
	}

	public function keyExist(string $key): bool {

		//return function($v) { return isset(this->strCollector[$key]) > }

		if (isset($this->strCollector[$key])) { return true; }

		$this->err['keyerr'][] = 'В массиве такого ключа не существует';

		return false;
	}

	// Проверяем существует ли переменная или нет в массиве 

	public function isNotEmpty(string $key): bool {

		if (empty($this->strCollector[$key]) || !isset($this->strCollector[$key])) {

			$this->err['strempty'][] = 'Переменная в массиве пустая!';
			return false;
		}

		return true;
	}

	// Проверяем меньше, чем указанно

	public function isNotLess(string $key, int $minnumber = 0): bool {

		if (!$this->keyExist($key)) { return false; }

		$minNum = $this->strCollector[$key]['minimum'];

		// Эта строка нужна только, если хотите указать сами какое кол-во нужно

		if ($minnumber > 0) { $minNum = $minnumber; }

		if(strlen($this->strCollector[$key]['value']) < $minNum){

			$this->err['smallerr'] = 'Слишком короткое значение!';
			return false;
		}

		return true;
	} 

	// Проверяем больше, чем указанно

	public function isNotMore(string $key, int $maxnumber = 0): bool {

		if (!$this->keyExist($key)) return false;

		$maxNum = $this->strCollector[$key]['maximum'];

		// Эта строка нужна только, если хотите указать сами какое кол-во нужно

		if ($maxnumber > 0) { $maxNum = $maxnumber; }

		if (strlen($this->strCollector[$key]['value']) > $maxNum) {

			$this->err['bigerr'] = 'Слишком большое значение!';
			return false;
		}

		return true;
	}

	public function convertToNumber(string $key): bool {

		if (!$this->keyExist($key)) return false; 

		$value = $this->strCollector[$key]['value'];

		$this->strCollector[$key]['value'] = intval($value);

		return true;
	}

	// Обрезаем стринговое значение 
	
	public function cutString(stirng $key): void {

		if (!$this->keyExist($key)) return;

		$string = $this->strCollector[$key]['value'];

		if (isMoreThen($key, $string)) {

			$string = substr($string, 0, $this->strCollector[$key]['maximum']);
		}

		$this->strCollector[$key]['value'] = $string;
	}


	function fixUrlValidation(string $url) {

		return preg_match('|^(http(s)?://)?[a-z0-9-]+\.(.[a-z0-9-]+)+(:[0-9]+)?(/.*)?$|i', $url);
	}
	
	// Проверяем это емайл или нет

	/*
	public function isItMail(string $key): bool {

		if (!$this->keyExist($key)) return false;

		$string = $this->strCollector[$key]['value'];

		if (!$this->validator($string, 'email')) {

			$this->err['noemail'] = 'Некорректный емайл.';

			return false;
		} 

		return true ;
	}
	*/

	
	/*
	PHP Validations Filters

	FILTER_VALIDATE_BOOLEAN		Checks for a valid Boolean value
	FILTER_VALIDATE_EMAIL		Checks for a valid email address
	FILTER_VALIDATE_FLOAT		Checks for a valid float value
	FILTER_VALIDATE_INT			Checks for a valid integer value
	FILTER_VALIDATE_IP			Checks for a valid IP address value
	FILTER_VALIDATE_REGEXP		Checks for a valid regular expression value
	FILTER_VALIDATE_URL			Checks for a valid URL string

	PHP Sanitation Filters 

	FILTER_SANITIZE_EMAIL				Removes illegal characters from an email address
	FILTER_SANITIZE_ENCODED				Encodes special characters in the string
	FILTER_SANITIZE_MAGIC_QUOTES 		Apply the addslashes() function
	FILTER_SANITIZE_NUMBER_FLOAT 		Remove all characters, except digits, +, –, and E
	FILTER_SANITIZE_NUMBER_INT			Removes all characters except digits and + or –
	FILTER_SANITIZE_SPECIAL_CHARS		Removes any special characters in the string
	FILTER_SANITIZE_FULL_SPECIAL_CHARS	Same as htmlspecialchars()
	FILTER_SANITIZE_STRING				Removes HTML tags and special characters from a string
	FILTER_SANITIZE_STRIPPED			Same as FILTER_SANITIZE_STRING
	FILTER_SANITIZE_URL					Removes all illegal characters from a URL string
	
	The PHP Filter Functions

	filter_has_var()		Checks if a variable of the specified type exists
	filter_id()				Returns the filter ID of the specified filter
	filter_input()			Retrieves a value passed by GET, POST, sessions, or cookies and filters it
	filter_input_array()	Retrieves multiple values passed to the PHP program and filters them
	filter_list()			Returns a list of supported filters
	filter_var()			Filters a variable
	filter_var_array()		Filters a list of variables

	*/

	
	public function validator(string $key, string $category): bool{

		if (!$this->keyExist($key)) { return false; }

		$input = $this->strCollector[$key]['value'];

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

	public function sanitizer(string $key, string $category): void {

		if (!$this->keyExist($key)) { return; }

		$input = $this->strCollector[$key]['value'];

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

		// Очистить все кроме букв
		// $str = preg_replace('/[^a-zA-Z]/', '', $input);
		// Очистить все кроме букв и цифр
		// $str = preg_replace('/[^a-zA-Z0-9\s]/', '', $mixed);

		$this->strCollector[$key]['value'] = filter_var($input, $sanitizer);
	}

	 // TODO: Метод временный удалить потом

	// Получаем все ошибки которые произошли при фильтрации 
	public function getErrors(): ?array { return $this->err; }

	// Отфильтровываем все спецсимволы
	// antisql, antixss, antirfi, antilfi, antishell
	public function cleanAttack(string $key, array $catatt): void {

		if (!$this->keyExist($key)) { return; }

		if(empty($catatt)) {
	
			$category = array(
				'antisql',
				'antixss',
				'antirfi', 
				'antilfi',
				'antishell'
			);
		}

		$result = function($symbols) use (&$input, &$filter) {

			foreach ($symbols as $k => $v) {
				
				$input = str_replace($v, '', $input);
			}
			//$filter = true;
		};

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

		$input = $this->strCollector[$key]['value'];

		//$filter = false;

		foreach ($catatt as $k => $v) {
			
			switch($v) {
				case 'antixss': 	$result($antixss); break;
				case 'antisql': 	$result($antixss); break;
				case 'antirfi': 	$result($antixss); break;
				case 'antilfi': 	$result($antixss); break;
				case 'antishell': 	$result($antixss); break;
			}
		}

		//if(!$filter) { return; }

		$input = htmlspecialchars($input);

		$this->strCollector[$key]['value'] = $input;
	}
}