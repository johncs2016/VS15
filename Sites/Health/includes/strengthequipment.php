<?php

	require_once("CRUD.php");

	class tblstrengthequipment extends pdoClass {
			
		protected $_id;
		protected $_equipname;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblstrengthequipment";
			$this->description = "Strength Equipment";
		}

		public function getID() {
			return $this->_id;
		}
		
		public function setID($value) {
			$this->_id = $value;
		}

		public function getName() {
			return $this->_equipname;
		}
		
		public function setName($value) {
			$this->_equipname = $value;
		}
	}
?>
