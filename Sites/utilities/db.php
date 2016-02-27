<?php

class DB {
	public static $instance = null;

	private 	$_pdo = null,
				$_query = null,
				$_error = false,
				$_results = null,
				$_count = 0;

	public function __construct() {
		try {
			$dsn = DB_DRIVER . ':host=' . DB_HOST . ';dbname=' . DB_NAME;
			$this->_pdo = new PDO($dsn, DB_USER, DB_PASS);
			$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		} catch(PDOException $e) {
			die('Connection error: ' . $e->getMessage());
		}
	}
	
		public static function getInstance() {
		// Already an instance of this? Return, if not, create.
		if(!isset(self::$instance)) {
			self::$instance = new DB();
		}
		return self::$instance;
	}

	public function query($sql, $params = array()) {

	$arr = explode(' ', $sql);
	$action = trim(strtoupper($arr[0]));
	
	$this->_error = false;

		if($this->_query = $this->_pdo->prepare($sql)) {
			$x = 1;
			if(count($params)) {
				foreach($params as $param) {
					$this->_query->bindValue($x, $param);
					$x++;
				}
			}

			if($this->_query->execute()) {
				if($action != 'INSERT' && $action != 'UPDATE' && $action != 'DELETE') {
					$this->_results = $this->_query->fetchAll();
					$this->_count = $this->_query->rowCount();
				} else {
					$id = $this->_pdo->lastInsertId();
					$this->_count = 1;
					$params['id'] = $id;
					$this_results = json_decode(json_encode($params), false);
				}
			} else {
				$this->_error = true;
			}
		}
		
		return $this;
	}

	public function get($table, $fields = array(), $where = array(), $operators = array(), $andor = array(), $order = null, $asc = true) {
		return $this->action($table, 'SELECT', $fields, $where, $operators, $andor, $order, $asc);
	}

	public function delete($table, $id) {
		return $this->action($table, 'DELETE', '', array('id' => $id));
	}

	public function action($table,
							$action = 'SELECT',
							$fields = array(),
							$where = array(),
							$operators = array(),
							$andor = array(),
							$order = null,
							$asc = true,
							$start = null,
							$rowcount = null
					) {

		$sql = new SQL($table, $action, $fields, $where, $operators, $andor, $order, $asc);
		if(!$this->query((string)$sql, $where)->error()) {
			if($action != 'SELECT') {
				return $this;
			} elseif(!$this->query((string)$sql . $this->limit($start, $rowcount), $where)->error()) {
				return $this;
			}
		}
			
		return false;
	}

	public function insert($table, $fields = array()) {
		
		$keys 	= array_keys($fields);
		$values = null;
		$x 		= 1;

		foreach($fields as $value) {
			$values .= "?";
			if($x < count($fields)) {
				$values .= ', ';
			}
			$x++;
		}

		$sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";
		
		if(!$this->query($sql, $fields)->error()) {
			return true;
		}

		return false;
	}

	public function update($table, $id, $fields = array()) {
		$set 	= null;
		$x		= 1;

		foreach($fields as $name => $value) {
			$set .= "{$name} = ?";
			if($x < count($fields)) {
				$set .= ', ';
			}
			$x++;
		}

		$sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";

		if(!$this->query($sql, $fields)->error()) {
			return true;
		}

		return false;
	}

	public function results() {
		// Return result object
		return $this->_results;
	}

	public function first() {
		return $this->_results[0];
	}

	public function last() {
		return $this->_results[$this->count - 1];
	}

	public function count() {
		// Return count
		return $this->_count;
	}

	public function error() {
		return $this->_error;
	}
	
	public function getPrimaryKey($table) {
		
		$result = $this->action($table, 'SHOW', array('INDEX'), array('Key_name' => 'PRIMARY'), array('='))->first();

		return $result->Column_name;
	}

	private function limit($start = null, $rowcount = null) {
		
		if($start == null && $rowcount == null) {
			$start = 1;
			$rowcount = $this->_count;
		} elseif ($start == null || $rowcount == null) {
			$rowcount = ($start == null ? $rowcount : $start);
			$start = 1;
		}
		
		if ($start < 1) {
			$start = 1;
		}
	
		$end = $start + $rowcount - 1;
		if ($end > $this->_count) {
			$rowcount = $this->_count - $start;
		} elseif ($end < 1) {
			$end = 1;
		}
		
		if ($start == 1 && $end == $this->_count) {
			$limit = "";
		} else {
			$limit = " LIMIT ";
			if ($start == 1) {
				$limit .= $rowcount;
			} else {
				$limit .= $start . ", " . $rowcount;
			}
		}
	
		return $limit;
	}
}
?>
