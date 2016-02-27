<?php
	require_once 'CRUD.php';
	
	class tbltwindex extends pdoClass {
		
		protected $_id;
		protected $_refpoint;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tbltwindex";
			$this->description = "References For Target Weight";
		}
		
		public function getID() {
			return $this->_id;
		}
		
		public function setID($value) {
			$this->_id = $value;
		}

		public function getRefPoint() {
			return $this->_refpoint;
		}
		
		public function setRefPoint($value) {
			$this->_refpoint = $value;
		}
	}
?>