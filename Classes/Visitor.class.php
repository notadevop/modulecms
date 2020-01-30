<?php 

/**
 * 
 */
class Visitor {
	
	function __construct() { }

	private $ua;
	private $lang;

	// TODO: Для нормальной работы статического метода использовать для 
	// переменнных self::

	public static function get_data(): array {

		return array(
			'ua' 	=> $_SERVER['HTTP_USER_AGENT'],
			'lang' 	=> $_SERVER['HTTP_ACCEPT_LANGUAGE']
		);
	}

	public static function get_userspecs(): array {

		return array(
			'userbrowser' 	=> self::get_browser(),
			'useros' 		=> self::get_os(),
			'userbrlang' 	=> self::get_lang(),
			'userremoteip' 	=> self::get_ip(),
		);
	}

	public static function get_lang(): string {

		return substr(self::get_data()['lang'], 0, 2);
	}

	public static function get_os(): string {

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
            '/macintosh|mac os x/i' =>  'Macos X',
            '/mac_powerpc/i'        =>  'Macos PPC',
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

	    foreach ($os_array as $regex => $value) { 

	        if (preg_match($regex, self::get_data()['ua'])) {
	            $os_platform = $value;
	        }
	    }
	    return $os_platform;
	}

	public static function get_ip() {

		// Check for shared Internet/ISP IP
	    if (!empty($_SERVER['HTTP_CLIENT_IP']) && self::validate_ip($_SERVER['HTTP_CLIENT_IP'])) {

	        return $_SERVER['HTTP_CLIENT_IP'];
	    }

	    // Check for IP addresses passing through proxies
	    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        // Check if multiple IP addresses exist in var
	        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
	            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	            foreach ($iplist as $ip) {
	                if (self::validate_ip($ip)) {
	                    return $ip;
	                }
	            }
	        } else {
	            if (self::validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
	                return $_SERVER['HTTP_X_FORWARDED_FOR'];
	        }
	    }

	    if (!empty($_SERVER['HTTP_X_FORWARDED']) 
	    	&& self::validate_ip($_SERVER['HTTP_X_FORWARDED']))
	        return $_SERVER['HTTP_X_FORWARDED'];

	    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) 
	    	&& self::validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
	        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
	    
	    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) 
	    	&& self::validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
	        return $_SERVER['HTTP_FORWARDED_FOR'];
	    
	    if (!empty($_SERVER['HTTP_FORWARDED']) 
	    	&& self::validate_ip($_SERVER['HTTP_FORWARDED']))
	        return $_SERVER['HTTP_FORWARDED'];
	    
	    // Return unreliable IP address since all else failed
	    return $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * Ensures an IP address is both a valid IP address and does not fall within
	 * a private network range.
	 */
	static function validate_ip(string $ip): bool {

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

	public static function get_browser(): string {

		$browser        = "Unknown Browser";  
	    $browser_array  = array(

	    	// -> EDGE, VIVALDI, CHROMIUM?
	    	
			'/safari/i'     =>  'Safari',
            '/msie/i'       =>  'Internet Explorer',
            '/edge/i'       =>  'Microsoft Edge',
            '/firefox/i'    =>  'Firefox',
            '/opera/i'      =>  'Opera',          
            '/netscape/i'   =>  'Netscape',
            '/maxthon/i'    =>  'Maxthon',
            '/konqueror/i'  =>  'Konqueror',
            '/chrome/i'     =>  'Chrome',
            '/OPR/i'        =>  'Opera',
            '/SeaMonkey/i'  =>  'SeaMonkey',
            '/mobile/i'     =>  'Handheld Browser'

            // TODO: Добавить новые браузеры
            /*
				'Opera',
				'Opera Mini',
				'WebTV',
				'Pocket Internet Explorer',
				'iCab',
				'OmniWeb',
				'Firebird',
				'Iceweasel',
				'Shiretoko',
				'Mozilla',
				'Amaya',
				'Lynx',
				'GoogleBot',
				'Yahoo! Slurp',
				'W3C Validator',
				'BlackBerry',
				'IceCat',
				'Nokia S60 OSS Browser',
				'Nokia Browser',
				'MSN Browser',
				'MSN Bot',
				'Netscape Navigator',
				'Galeon',
				'NetPositive',
				'Phoenix',
			*/

		);

	    foreach ($browser_array as $regex => $value) { 

	        if (preg_match($regex, self::get_data()['ua'])) {
	            $browser = $value;
	        }
	    }
	    return $browser;
	}

	

	private function usersOnlineStorageMysql(): int { 

		// MySQL database -----------------------------

		if (session_id() == '')  
			session_start();
		
		$visitor 	= new Visitor();
		$db			= new Database();

		$p = array(
			'session' => session_id(),
			'vistime' => time(),
			'outtime' => (time() - 60),
			'userip'  => $visitor->get_ip()
		);

		// Создаем таблицу, если не существует 

		$sql = 'CREATE TABLE IF NOT EXISTS `users_online` (
	  		`session` 	varchar(255) NOT NULL,
	  		`visitime`  varchar(255) NOT NULL,
	  		`userip` 	varchar(255) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

		$db->preAction($sql);
		$db->doAction();

		// Проверяем существует ли пользователь указанный в session_id
		$sql = 'SELECT COUNT(*) as count FROM users_online WHERE session = :session';

		$binder = array(':session' => $p['session']);

		$db->preAction($sql, $binder);

		$db->doAction();

		$r = $db
			->postAction()
			->fetchColumn();

		// Обновляем или добавляем id пользователя

		if ($r['count'] > 0) 
			$sql = 'UPDATE users_online SET visitime=:vistime, userip=:uip WHERE session = :sess';
		else 
			$sql = 'INSERT INTO users_online (visitime, userip, session) VALUES (:vistime, :uip, :sess)';

		$binder = array(
			':vistime'	=> $p['vistime'],
			':uip'		=> $p['userip'],
			':sess'		=> $p['session']
		);

		$db->preAction($sql, $binder);
		$db->doAction();

		$sql = 'DELETE FROM users_online WHERE visitime < :vistime';

		$binder = array(':vistime' => $p['vistime']);

		$db->preAction($sql, $binder);
		$db->doAction();

		$sql = 'SELECT COUNT(*) as count FROM users_online';

		$db->preAction();

		$db->doAction();

		$people = $db
					->postAction()
					->fetch(); // fetchColumn();

		return $people['count'];
	} 

	private function usersOnlineStorageSqlite(bool $stats=false): int { 

		//SQLite 
		// Cпециально для SQLite базы создаем папку и в ней базу
		$files = new FilesManipulator();

		$workinDir = ROOTPATH.SQLITEJOB['sqlitefolder'];

		$files->set_dir_name($workinDir);
		$files->create_dir();

		if (session_id() == '') 
			session_start();
		
		$p = array(
			'session' => session_id(),
			'vistime' => time(),
			'outtime' => (time() - 60),
			'userip'  => self::get_ip(),
			'uagent'  => serialize(self::get_userspecs()),
			'sqlfile' => ($workinDir.DIRECTORY_SEPARATOR.SQLITEJOB['sqlitefile'])
		);

		$sqlite = new Sqlitedbase($p['sqlfile']);

		$sqlite->exec('CREATE TABLE IF NOT EXISTS tmp_users_online (
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

	    $objusers->bindValue(':curtime', $p['vistime'], SQLITE3_TEXT);
	    $objusers->bindValue(':addr', $p['userip'], SQLITE3_TEXT);
	    $objusers->bindValue(':uagent', $p['uagent'], SQLITE3_TEXT);
	    $objusers->bindValue(':sess', $p['session'], SQLITE3_TEXT);

	    $objusers->execute();

	    $sql = 'DELETE FROM tmp_users_online WHERE usertime < :tmout';

	    $deluser = $sqlite->prepare($sql);
	    $deluser->bindValue(':tmout', $p['outtime'], SQLITE3_TEXT);
	    $deluser->execute();

	    $sql 	= 'SELECT COUNT(*) as count FROM tmp_users_online';
	    $result = $sqlite->querySingle($sql);

	    return $result['count'];

	}


	// storage: mysql, sqlite <= для получения статистики даже когда сайт упал
	public static function users_online(string $storage='mysql'): int {

		// TODO: переключение на sqlite, если невозможно подключиться к базе данных mysql

		$onlineNow = 0;

		if ($storage = 'mysql') {

			$onlineNow = self::usersOnlineStorageMysql();
		} else {

			$onlineNow = self::usersOnlineStorageSqlite();
		}


		return $onlineNow;
	}


}