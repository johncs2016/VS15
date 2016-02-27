<?php

	require_once("CRUD.php");

	class tblcardioprogs extends pdoClass {
			
		protected $_id;
		protected $_program;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblcardioprogs";
			$this->description = "Cardio Programs";
		}

		public function getID() {
			return $this->_id;
		}
		
		public function setID($value) {
			$this->_id = $value;
		}

		public function getProgram() {
			return $this->_program;
		}
		
		public function setProgram($value) {
			$this->_program = $value;
		}
	}
?>
