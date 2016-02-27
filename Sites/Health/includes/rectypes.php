<?php
	
	require_once "CRUD.php";
	
	class tblrectypes extends pdoClass {
		
		protected $_id;
		protected $_type;

		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblrectypes";
			$this->description = "Recipient Types";
		}
		
		public function getID() {
			return $this->_id;
		}
		
		public function setID($value) {
			$this->_id = $value;
		}

		public function getType() {
			return $this->_type;
		}
		
		public function setType($value) {
			$this->_type = $value;
		}
	}
?>


