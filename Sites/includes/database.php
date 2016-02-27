<?php

	require_once("config.php");
	
	class database {
		
		protected $_host;
		protected $_dbname;
		protected $_username;
		protected $_password;
		protected $_tablename;
		
		public function __construct($config, $table) {
			$this->host = $config['hostname'];
			$this->dbname = $config['dbname'];
			$this->username = $config['username'];
			$this->password = $config['password'];
			$this->tablename = $table;
		}
		
		public function connect() {
			$dsn = 'mysql:host='.$this->host.';dbname='.$this->dbname;
			$db = new PDO($dsn, $config['db']['username'], $config['db']['password']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			try {
				
			}
			catch(PDOException $e) {
				return false;
			}
		}
	}
?>