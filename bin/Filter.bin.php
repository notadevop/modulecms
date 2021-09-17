<?php 

/** 

	Фильтрационный класс для фильтрации входяших данных
*/



class Filter {
	
	function __construct() {

		print('this is bin filter!');
	}
	function __destruct() { }

	// Провеоряем существует ли переменная и находиться в ней что нибуть

	function isNotEmpty($param): bool {

		return isset($param) && !empty($param) ? true : false;
	}

	// Очищаем массив от пустых значений -------------------------------

	function cleanEmptyArray(array $arr): ?array { 

		if (!$this->isNotEmpty($arr) || !is_array($arr)) {return null;}
	}

	// Проверяем больше ли значение чем указанно

	function isMoreThan($param, int $maxScore): bool {

		return (!$this->isNotEmpty($param) || strlen($param) > $maxScore) ? false : true;
	}

	// Проверяем меньше ли значение чем указанно 

	function isLessThen($param, int $minScore): bool {

		return (!$this->isNotEmpty($param) || strlen($param) < $minScore) ? false : true;
	}

	// Обрезаем стринговое значение

	function cutString(string $param, int $maxScore): ?string {

		if(!$this->isNotEmpty($param) || !$this->isMoreThan($param, $maxScore)){return $param;}

		return substr($param, 0, $maxScore);
	}

	// Нужно проверить, что он возвращает должен вроде bool

	function urlValidation(string $param):bool {

		if(!$this->isNotEmpty($param)) {return false;}

		return !preg_match('|^(http(s)?://)?[a-z0-9-]+\.(.[a-z0-9-]+)+(:[0-9]+)?(/.*)?$|i', $param)? false : true;
	}

	function mainValidator($param, string $category): bool {

		if(!$this->isNotEmpty($param)) {return false;}

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

		return !filter_var($param, $validator) ? false : true ;	
	}

	function mainSanitizer($param, string $category) {

		if(!$this->isNotEmpty($param)) {return false;}

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

		return filter_var($param, $validator);
	}

	function ejectedWords(string $param, $catWords='all'): ?string {

		if(!$this->isNotEmpty($param)) {return null;}

		// Basic Hacking Words

		$blockedWords = array(

			'antixss' => array(
				// AntiXSS 
				"/script/i",
				"/</i",
				"/>/i",
				"/alert/i",
				"/img/i",
				"/cookie/i",
				"/href/i",
				"/input/i",
				"/form/i"
			),
			'antisql' => array(
				// AntiSQL
				"/--/i",
				"/;/i",
				"/'/i",
				'/"/i',
				"/0x/i",
				"/@@/i",
				"/alter/i",
				"/char/i",
				"/begin/i",
				"/cast/i",
				"/create/i",
				"/cursor/i",
				"/declare/i",
				"/delete/i",
				"/drop/i",
				"/end/i",
				"/fetch/i",
				"/insert/i",
				"/kill/i",
				"/open/i",
				"/select/i",
				"/sys/i",
				"/update/i",
				"/union/i",
				"/or/i",
				"/from/i",
				"/like/i",
				"/and/i",
				"/all/i",
				"/group_concat/i",
				"/order/i",
				"/version/i",
				"/by/i",
				"/table/i",
				"/database/i"
			),
			'antirfi' => array(
				// AntiRFI and outgoing scripts like .php .js etc..
				"/http/i",
				"/.php/i",
				"/.js/i",
				"/.asp/i",
				"/phtm/i"
			),
			'antilfi' => array(
				// AntiLFI 
				"/etc/i",
				"/passwd/i",
				"/proc/i",
				"/self/i",
				"/environ/i"
			),
			'antishell'=> array(
				// AntiSHELL 
				"/passthru/i",
				"/exec/i",
				"/shell/i",
				"/open/i",
				"/load_file/i",
				"/system/i",
				"/show_source/i"
			)
		);

		$catWords = $catWords == 'all' ? array_keys($blockedWords) : array_keys($blockedWords, $catWords);

		if(empty($catWords) || !is_array($catWords)) { throw new Exception("Указанн неправильно параметр", 1);
		 }

		$cleaner = function (array $patterns, string $input) {

			return preg_replace($patterns, '"'.$input.'"', $input);
		};

		foreach ($catWords as $k => $v) {
			
			switch($v) {
				case 'antixss': 	$param = $cleaner($antixss, $param); break;
				case 'antisql': 	$param = $cleaner($antisql, $param); break;
				case 'antirfi': 	$param = $cleaner($antirfi, $param); break;
				case 'antilfi': 	$param = $cleaner($antilfi, $param); break;
				case 'antishell': 	$param = $cleaner($antishell, $param); break;
			}
		}
		
		return $param;
	}

	function shieldingData($param): string {

		return $param;
	}

	function keepParams(string $key, string $category) {

		// Очистить все кроме букв
		// $str = preg_replace('/[^a-zA-Z]/', '', $input);
		// Очистить все кроме букв и цифр
		// $str = preg_replace('/[^a-zA-Z0-9\s]/', '', $mixed);
	}

}