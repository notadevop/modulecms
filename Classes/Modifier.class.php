<?php 


/**
 * 		Простой пример шифрования XOR: 
 * 		Взято с: https://denik.od.ua/xor_text_encoding
 */
class Modifier {
	
	function __construct() {}

	// Возвращает хеш стрингового значения 
	function strToHash(string $input): string {

		// TODO: сделать нормальный хеш

		// Если сменить в корне настроек убивает все сохраненные хеши

		$solt 		= SOLT; 
		$privkey 	= PRIVATEKEY;

		//return hash('sha512', $input.$solt.$privkey);
		return hash('sha512', $input.$solt);
	}

	// TODO: Временная заглушка для аутентификации, нужно переделать 

	// Нужен для аутентефикации пользователя, не используеться без токена 
	public function createFingerprint(string $input, string $subinput): string {

		$finger = $this->strToHash($input.$subinput);

		$finger = $this->encode($finger, PUBLICKEY); // ?????

		return $finger; 
	}

	// Возвращает случайный набор символов при указанном количестве символов

	public function randomHash(int $length=6, bool $specsym=false): string {

		// Генерируем произвольный код для случайного значения
	    $chars 	= '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ';

	    if($specsym) 
	    	$chars .= '!@#$%^&*()_+=-';

	    $code 	= '';
	    $slen 	= strlen($chars) - 1;

	    while (strlen($code) < $length) {

	    	$code .= $chars[mt_rand(0,$slen)]; 
		}
	    return $code;
	}

	/* шифрует и расшифровывает стринговое значение */

	private function cryptor(string $str, string $passw=''): string {

	   	$salt 	= SOLT; 
	   	$len 	= strlen($str);
	   	$gamma 	= ''; 
		$gamma .= sha1($passw . $gamma);
	   	$n 		= $len > 100 ? 8 : 2;
	   	
	   	while( strlen($gamma) < $len ) {

	    	$gamma .= substr(pack('H*', sha1($passw.$gamma.$salt)), 0, $n);
	   	}
	   	return $str^$gamma;
	}

	// base_64 

	public function encode(string $input, string $passwd): string {

		return base64_encode($this->cryptor($input, $passwd));
	}

	public function decode(string $input,string $passwd): string {

		return $this->cryptor(base64_decode($input), $passwd);
	}
}