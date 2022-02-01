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
		    // Выключает режим эмуляции! проблемы с LIMIT ?,?
		    PDO::ATTR_EMULATE_PREPARES 	 	=> false, 
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
				debugger($e->getMessage());
			}
			die('Database connection error!');
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

		// Обнуляем при каждой подготовке, если пустое!
		$this->prepared = !empty($prepared) ? $prepared : null;
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
			/*
			vardump($this->sql);
			vardump($this->prepared);
			echo '<hr/>';
			*/

			if (!empty($this->prepared)) {

				foreach ($this->prepared as $key => $value) {

					//PDO::PARAM_BOOL
					//PDO::PARAM_NULL
					// is_float
					// is_numeric
					// is_null
					// is_array
					// is_object

					
					switch(true) {
						case is_string($value):
							$type = PDO::PARAM_STR;
						break;
						case (is_int($value) || is_numeric($value)):
							$type = PDO::PARAM_INT;
						break;
						case is_null($value):
							$type = PDO::PARAM_NULL;
						break;
						case is_bool($value):
							$type = PDO::PARAM_BOOL;
						break;
						case (is_float($value) || is_double($value)):
							$type = PDO::PARAM_FLOAT;
						break;
						default:
							$type = PDO::PARAM_STR;
						break;
					}

					$this->stmt->bindValue($key, $value, $type);

					//$this->stmt->bindParam($key, $value);
					//$this->stmt->bindValue($key, $value);
				} 

				$this->prepared = null;
				$this->sql 		= null;
			} 

			if (!$this->stmt->execute()) 
				throw new RuntimeException(DBERRQUERY.' - '.$this->stmt->errorCode());
			$r = true;
		} catch (Exception $e) {

			echo '<pre>';
			if (DEBUG){
				print_r($this->sql);
				echo '<br/>';
			}
			print_r($e->getMessage().'<br/>');
			echo '</pre>';
			echo '<hr/>';
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