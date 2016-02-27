<?php
	require_once 'CRUD.php';
	
	class tblgenders extends pdoClass {
		
		protected $_id;
		protected $_gender;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblgender";
			$this->description = "Genders";
		}
		
		public function getID() {
			return $this->_id;
		}
		
		public function setID($value) {
			$this->_id = $value;
		}

		public function getGender() {
			return $this->_gender;
		}
		
		public function setGender($value) {
			$this->_gender = $value;
		}
	}
?>
