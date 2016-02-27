<?php

	require_once("CRUD.php");

	class tblstrengthprogs extends pdoClass {
			
		protected $_id;
		protected $_userid;
		protected $_equipid;
		protected $_weight;
		protected $_sets;
		protected $_reps;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblstrengthprogs";
			$this->description = "User Strength Program";
		}

		public function getID() {
			return $this->_id;
		}
		
		public function setID($value) {
			$this->_id = $value;
		}

		public function getUserID() {
			return $this->_userid;
		}
		
		public function setUserID($value) {
			$this->_userid = $value;
		}

		public function getEquipmentID() {
			return $this->_equipid;
		}
		
		public function setEquipmentID($value) {
			$this->_equipid = $value;
		}

		public function getWeight() {
			return $this->_weight;
		}
		
		public function setWeight($value) {
			$this->_weight = $value;
		}

		public function getSets() {
			return $this->_sets;
		}
		
		public function setSets($value) {
			$this->_sets = $value;
		}

		public function getReps() {
			return $this->_reps;
		}
		
		public function setReps($value) {
			$this->_reps = $value;
		}
	}
?>
