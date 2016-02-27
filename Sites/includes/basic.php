<?php

	require_once("CRUD.php");
 
 	class tblbasicmeasurements extends pdoClass {
		
		protected $_id;
		protected $_name;
		protected $_description;
		protected $_value;
		protected $_typeid;
		protected $_userid;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblbasicmeasurements";
			$this->description = "Basic Measurements";
		}
		
		public function getID() {
			return $this->_id;
		}
		
		public function setID($value) {
			$this->_id = $value;
		}

		public function getName() {
			return $this->_name;
		}
		
		public function setName($value) {
			$this->_name = $value;
		}

		public function getDescription() {
			return $this->_description;
		}
		
		public function setDescription($value) {
			$this->_description = $value;
		}

		public function getValue() {
			return $this->_value;
		}
		
		public function setValue($value) {
			$this->_value = $value;
		}

		public function getTypeID() {
			return $this->_typeid;
		}
		
		public function setTypeID($value) {
			$this->_typeid = $value;
		}

		public function getUserID() {
			return $this->_userid;
		}
		
		public function setuserID($value) {
			$this->_userid = $value;
		}
	}
?>