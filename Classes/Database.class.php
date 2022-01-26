<?php 

/**
 * mysql database connector 
 */
class Database {

	function __construct(bool $connect) {	
		// Устанавливаем соединение, только если указали на подключение
		if ($connect) { $this->make_con(); }
	}

	function __destruct() {
		unset($link);
		unset($sql);
		unset($sqlData);
		unset($stmt);
	}

	private $sql;		// Хронит SQL запрос с индексами 
	private $prepared; 	// хранит индексы запроса например :user или :score
	private $link;		// Хранит соединение запроса 
	private $stmt;		// Хронит результат запроса для последущей обр. в виде Object

	private function make_con(): void {

		/*
		if ($db->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
    		$stmt = $db->prepare('select * from foo',
        	array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
		} else {
		    die("...");
		}
		*/

		$dsn = 'mysql:host='.DBHOST.';dbname='.DBNAME.';charset='.DBCHAR;
		$opt = array(
			PDO::ATTR_PERSISTENT		 	=> true,
		    PDO::ATTR_ERRMODE            	=> PDO::ERRMODE_EXCEPTION,
		    PDO::ATTR_DEFAULT_FETCH_MODE 	=> PDO::FETCH_ASSOC,
		    PDO::ATTR_EMULATE_PREPARES 	 	=> false, // Выключает режим эмуляции! проблемы с LIMIT ?,?
		    PDO::ATTR_CURSOR 				=> PDO::CURSOR_FWDONLY
		);

		try {
			$link = new PDO($dsn, DBUSER, DBPASS, $opt);
			if (!$link) {
				throw new Exception(DBERRCONN, 4);
			}

			if ($link->getAttribute(PDO::ATTR_DRIVER_NAME) != DBENGINE) {
				throw new Exception(DBENGINEERR, 4);
			}

			$this->link = $link;
		
		} catch (Exception $e) {

			if (DEBUG) {
				//debugger($e->getMessage());
			}
			//die(DBERRINFO.' '.$e->getMessage());
			die($e->getMessage());
		}
	}

	// Возвращает соединение к базе данных в виде обьекта

	public function get_con(): PDO {
		return $this->link; 
	}

	// пример: $sql = 'SELECT table_id FROM table WHERE abc LIKE :abc AND a = :a OR b = :b ';

	public function preAction(string $sql, array $prepared=array()): void {
		// TODO: сделать фильтрацию типа mysql_escape_string();
		$this->sql = $sql;
		if (!empty($prepared))
			$this->prepared = $prepared;
	}

	// Онулируем все переменнные, при запуске данного метода!

	public function resetAction(): void {

		$this->sql = $this->prepared = $this->stmt = null;
	}

	public function doAction(): bool{

		$r = false;
		
		try {
			if (empty($this->sql)) 
				throw new RuntimeException(DBEMPTYSQL);

			$this->stmt = $this->get_con()->prepare($this->sql);
			
			if (!$this->stmt) 
				throw new RuntimeException(DBERRPREPQUERY.' - '.$this->stmt->errorCode());

			if (!empty($this->prepared)) {
				foreach ($this->prepared as $key => $value) {
					//$this->stmt->bindParam($key, $value);
					$this->stmt->bindValue($key, $value);
				}
			} 
			if (!$this->stmt->execute()) 
				throw new RuntimeException(DBERRQUERY.' - '.$this->stmt->errorCode());
			$r = true;
		} catch (Exception $e) {
			print_r($e->getMessage().'<br/>');
		}
		return $r;
	}

	function postAction() {
		return $this->stmt;
	}

	/*
		переменные для выполнения запроса SQL
		INSERT, UPDATE, SELECT, DELETE, DROP, etc

		$sql = 'SELECT * FROM table WHERE a = ? and b = ?';
		$data = array('a', 'b');
		
		$this->sql($sql);				// Готовим запрос
		$this->sqlData($data); 		// Готовим параметры
		// Исполняем запрос 
		$this->execute();							
		$this->execute()->fetch(); // Получаем ряд
		$this->execute()->fetchColumn(); // Получаем колонку
		$this->execute()->fetchAll(PDO::FETCH_COLUMN); // Получаем колонку
		$this->execute()->fetchAll(); // Получаем все ячейки
		// получаем ввиде пермен
		foreach ($result as $row) 
			echo $row['name'];
	*/
}