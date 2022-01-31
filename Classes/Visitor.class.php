<?php 

/**
 * 
 */
class Visitor extends Database {
	
	function __construct() { 
		parent::__construct(true);
	}

	private $ua;
	private $lang;

	// TODO: Для нормальной работы статического метода использовать для 
	// переменнных self::

	public function getUA(): string {

		return $_SERVER['HTTP_USER_AGENT'];
	}

	public function getLang(): string {

		return substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	}

	public function getOS(): string {

		/*
				
			1. Найти семейство операционной системы 
				(Linux,MacOS,Windows,Dos,WebOS etc...)
			2. Найти тип операционной системы 
				Linux->Ubuntu,Slackware... 
				MacOS->Intel,PPC,M1,M2.. 10.1, 10.9,11.1 etc...

			Cохранить все в многомерный массив

		*/
	
		$os_platform = "Unknown OS"; 
	    $os_array = array(
	    	'/Windows NT 10/i'     	=>  'Windows 10',
	    	'/Windows NT 10.0/i'    =>  'Windows 10',
            '/Windows NT 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows xp/i'         =>  'Windows XP',
            '/windows me/i'         =>  'Windows ME',
            '/Windows CE/i'			=>  'Windows CE',
            '/Windows/i'			=> 	'Windows Platform',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/Mac OS X 11/i'		=> 	'MacOS 11',
            '/Intel Mac OS X/i'		=>  'MacOS X Intel',
            '/M1 Mac OS X/i'		=> 	'MacOS Arm m1',
            '/mac_powerpc/i'        =>  'Macos PPC',
            '/macintosh/i' 			=>  'Macos X',
            '/iphone/i'             =>  'iOS/iPhone',
            '/ipod/i'               =>  'iOS/iPod',           
            '/ipad/i'               =>  'iOS/iPad',
            '/apple/i'				=> 	'Apple Device',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/freebsd/i'            =>  'FreeBSD',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'WebOS/Mobile',
            '/openbsd/i'			=> 	'OpenBSD',
            '/netbsd/i'				=> 	'NetBSD',
            '/sunos/i'				=>	'SunOS',
            '/opensolaris/i'		=>	'OpenSolaris',
            '/nokia/i'				=>	'Nokia',
            '/OS/2/i'				=> 	'OS/2',
            '/beos/i'				=> 	'BeOS'
            // TODO: Добавить новые операционные системы
	    );

	    $ua = $this->getUA();

	    foreach ($os_array as $regex => $value) { 

	        if (preg_match($regex, $ua)) { $os_platform = $value; break; }
	    }
	    return $os_platform;
	}

	public function getIP() {

		// Check for shared Internet/ISP IP
	    if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->validate_ip($_SERVER['HTTP_CLIENT_IP'])) {

	        return $_SERVER['HTTP_CLIENT_IP'];
	    }

	    // Check for IP addresses passing through proxies
	    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        // Check if multiple IP addresses exist in var
	        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
	            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	            foreach ($iplist as $ip) {
	                if ($this->validate_ip($ip)) {
	                    return $ip;
	                }
	            }
	        } else {
	            if ($this->validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
	                return $_SERVER['HTTP_X_FORWARDED_FOR'];
	        }
	    }

	    if (!empty($_SERVER['HTTP_X_FORWARDED']) 
	    	&& $this->validate_ip($_SERVER['HTTP_X_FORWARDED']))
	        return $_SERVER['HTTP_X_FORWARDED'];

	    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) 
	    	&& $this->validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
	        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
	    
	    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) 
	    	&& $this->validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
	        return $_SERVER['HTTP_FORWARDED_FOR'];
	    
	    if (!empty($_SERVER['HTTP_FORWARDED']) 
	    	&& $this->validate_ip($_SERVER['HTTP_FORWARDED']))
	        return $_SERVER['HTTP_FORWARDED'];
	    
	    // Return unreliable IP address since all else failed
	    return $_SERVER['REMOTE_ADDR'];
	}

	function validate_ip(string $ip): bool {

	    if (strtolower($ip) === 'unknown')
	        return false;

	    // Generate IPv4 network address
	    $ip = ip2long($ip);

	    // If the IP address is set and not equivalent to 255.255.255.255
	    if ($ip !== false && $ip !== -1) {
	        // Make sure to get unsigned long representation of IP address
	        // due to discrepancies between 32 and 64 bit OSes and
	        // signed numbers (ints default to signed in PHP)
	        $ip = sprintf('%u', $ip);

	        // Do private network range checking
	        if ($ip >= 0 && $ip <= 50331647)
	            return false;
	        if ($ip >= 167772160 && $ip <= 184549375)
	            return false;
	        if ($ip >= 2130706432 && $ip <= 2147483647)
	            return false;
	        if ($ip >= 2851995648 && $ip <= 2852061183)
	            return false;
	        if ($ip >= 2886729728 && $ip <= 2887778303)
	            return false;
	        if ($ip >= 3221225984 && $ip <= 3221226239)
	            return false;
	        if ($ip >= 3232235520 && $ip <= 3232301055)
	            return false;
	        if ($ip >= 4294967040)
	            return false;
	    }
	    return true;
	}

	public function getBrowser(): string {

		$browser        = "Unknown Browser";  
	    $browser_array  = array(

			'/safari/i'     				=> 'Safari',
            '/msie/i'       				=> 'Internet Explorer',
            '/edge/i'       				=> 'Microsoft Edge',
            '/Firefox/i'    				=> 'Firefox',
            '/opera/i'      				=> 'Opera',          
            '/netscape/i'   				=> 'Netscape',
            '/maxthon/i'    				=> 'Maxthon',
            '/konqueror/i'  				=> 'Konqueror',
            '/chrome/i'     				=> 'Chrome',
            '/OPR/i'        				=> 'Opera',
            '/SeaMonkey/i'  				=> 'SeaMonkey',
            '/mobile/i'     				=> 'Handheld Browser',
			'/Opera/i'						=> 'Opera',
			'/Opera Mini/i'					=> 'Opera Mini',	
			'/WebTV/'						=> 'WebTV',
			'/Pocket Internet Explorer/i'	=> 'Pocket IE',
			'/iCab/i'						=> 'iCab',
			'/OmniWeb/i'					=> 'OmniWeb',
			'/Firebird/i'					=> 'Firebird',
			'/Iceweasel/i'					=> 'IceWeasel',
			'/Shiretoko/i'					=> 'Shiretoko',
			'/Mozilla/i'					=> 'Mozilla',
			'/Amaya/i'						=> 'Amaya',
			'/Lynx/i'						=> 'Lynx',
			'/GoogleBot/i'					=> 'GoogleBot',
			'/Yahoo! Slurp/i'				=> 'Yahoo Slurp',
			'/W3C Validator/i'				=> 'W3C Validator',
			'/BlackBerry/i'					=> 'BlackBerry',
			'/IceCat/i'						=> 'IceCat',
			'/Nokia S60 OSS Browser/i'		=> 'Nokia S60 OSS Browser',
			'/Nokia Browser/i'				=> 'Nokia Browser',
			'/MSN Browser/i'				=> 'MSN Browser',
			'/MSN Bot/i'					=> 'MSN Bot',
			'/Netscape Navigator/i'			=> 'Netscape Navigatior',
			'/Galeon/i'						=> 'Galeon',
			'/NetPositive/i'				=> 'NetPositive',
			'/Phoenix/i'					=> 'Phoenix',

		);

	    foreach ($browser_array as $regex => $value) { 

	        if (preg_match($regex, $this->getUA())) {
	            $browser = $value;
	        }
	    }
	    return $browser;
	}


	public function getOnlineUsers() {

		$sql = 'SELECT `session`, `visitime`, `userip`, `uagent` FROM `users_online`';

		$this->preAction($sql);

		if(!$this->doAction()) return -1;

		return $this->postAction()->fetchAll();
	}
	

	protected function updateUsersOnline(): bool { 

		// MySQL database -----------------------------

		if (session_id() == '') 
			session_start();

		$userspecs['userbrowser'] 	= $this->getBrowser();
		$userspecs['useros'] 		= $this->getOS();
		$userspecs['userbrlang'] 	= $this->getLang();
		$userspecs['userremoteip'] 	= $this->getIP();


		$p = array(
			'session' 	=> (string)	session_id(),
			'vistime' 	=> (string)	time(),
			'outtime' 	=> (string)	(time() - 60), //
			'userip'  	=> (string)	$this->getIP(),
			'uagent' 	=> (string)	serialize($userspecs),
		);

		// Проверяем существует ли пользователь указанный в session_id
		$sql = 'SELECT COUNT(*) as count FROM users_online 
										WHERE session = :session';

		$binder = array(':session' => $p['session']);

		$this->preAction($sql, $binder);
		$this->doAction();

		// кажеться возвращает интигер без имени массива !
		$r = $this->postAction()->fetchColumn(); 

		// Обновляем или добавляем id пользователя

		if (!$r || $r < 1) {

			$sql = 'INSERT INTO users_online (visitime, userip, session, uagent) VALUES (:vistime, :uip, :sess, :uagent)';
		} else{ 

			$sql = 'UPDATE users_online SET visitime=:vistime, userip=:uip, uagent=:uagent WHERE session = :sess';
		}

		$binder = array(
			':vistime'	=> (string)$p['vistime'],
			':uip'		=> (string)$p['userip'],
			':sess'		=> (string)$p['session'],
			':uagent'	=> (string)$p['uagent']
		);

		$this->preAction($sql, $binder);
		$this->doAction();

		$sql = 'DELETE FROM users_online WHERE visitime < :vistime';

		$binder = array(':vistime' => $p['outtime']);

		$this->preAction($sql, $binder);

		return !$this->doAction() ? false : true;
	} 



	// SQLite stats хранилище для отслеживания пользователей
	// если не работает MySQL база данных

	/*
	protected function usersOnlineStorageSqlite(bool $stats=false): int { 

		//SQLite 
		// Cпециально для SQLite базы создаем папку и в ней базу
		$files = new Filemanipulator();

		$workinDir = ROOTPATH.SQLITEFOLDER;

		$files->setDirName($workinDir);
		$files->createDir();

		if (session_id() == '') 
			session_start();
		
		$p = array(
			'session' => session_id(),
			'vistime' => time(),
			'outtime' => (time() - 60),
			'userip'  => $this->get_ip(),
			'uagent'  => serialize($this->get_userspecs()),
			'sqlfile' => ($workinDir.DS.SQLITEFILE)
		);

		$sqlite = new Sqlitec($p['sqlfile']);

		$sqlite->query_exec('CREATE TABLE IF NOT EXISTS tmp_users_online (
			`session` 	varchar(255) NOT NULL,
			`usertime` 	varchar(255) NOT NULL,
			`ip` 		varchar(255) NOT NULL,
			`useragent` varchar(255) NOT NULL
		);');

		$sql = 'SELECT COUNT(*) as count FROM tmp_users_online WHERE session = :sess';

		$getusers = $sqlite->prepare($sql);

		$getusers->bindValue(':sess', $p['session'], SQLITE3_TEXT);

		$result = $getusers->execute();

		$u_online = $result->fetchArray(SQLITE3_ASSOC)['count'];

	    if ($u_online > 0) {
	    	$sql = 'UPDATE tmp_users_online 
	    	SET usertime = :curtime, ip = :addr, useragent = :uagent 
	    	WHERE session = :sess';
	    } else {
	    	$sql = 'INSERT INTO tmp_users_online (usertime, ip, useragent, session) 
	    	VALUES (:curtime, :addr, :uagent, :sess)';
	    }

	    $objusers = $sqlite->prepare($sql);

	    $objusers->bindValue(':curtime',$p['vistime'], 	SQLITE3_TEXT);
	    $objusers->bindValue(':addr', 	$p['userip'], 	SQLITE3_TEXT);
	    $objusers->bindValue(':uagent', $p['uagent'], 	SQLITE3_TEXT);
	    $objusers->bindValue(':sess', 	$p['session'], 	SQLITE3_TEXT);

	    $objusers->execute();

	    $sql = 'DELETE FROM tmp_users_online WHERE usertime < :tmout';

	    $deluser = $sqlite->prepare($sql);
	    $deluser->bindValue(':tmout', 	$p['outtime'], 	SQLITE3_TEXT);
	    $deluser->execute();

	    $sql 	= 'SELECT COUNT(*) as count FROM tmp_users_online';
	    $result = $sqlite->querySingle($sql);

	    return $result['count'];
	}
	*/
}