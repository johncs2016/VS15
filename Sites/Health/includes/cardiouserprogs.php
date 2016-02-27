<?php

	require_once("CRUD.php");

	class tblcardiouserprogs extends pdoClass {
			
		protected $_id;
		protected $_userid;
		protected $_equipid;
		protected $_level;
		protected $_progid;
		protected $_minutes;
		protected $_speed;
		protected $_incline;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblcardiouserprogs";
			$this->description = "User Cardio Programmes";
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

		public function getLevel() {
			return $this->_level;
		}
		
		public function setLevel($value) {
			$this->_level = $value;
		}

		public function getProgID() {
			return $this->_progid;
		}
		
		public function setProgID($value) {
			$this->_progid = $value;
		}

		public function getMinutes() {
			return $this->_minutes;
		}
		
		public function setMinutes($value) {
			$this->_minutes = $value;
		}

		public function getSpeed() {
			return $this->_speed;
		}
		
		public function setSpeed($value) {
			$this->_speed = $value;
		}

		public function getIncline() {
			return $this->_incline;
		}
		
		public function setIncline($value) {
			$this->_incline = $value;
		}
	}
?>
