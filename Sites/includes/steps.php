<?php

	require_once("CRUD.php");

	class tblstepscounts extends pdoClass {
			
		protected $_id;
		protected $_userid;
		protected $_obsdate;
		protected $_numberofstepswalked;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblstepscounts";
			$this->description = "Steps Counts";
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

		public function getObsDate() {
			return $this->_obsdate;
		}
		
		public function setObsDate($value) {
			$this->_obsdate = $value;
		}

		public function getStepsCount() {
			return $this->_numberofstepswalked;
		}
		
		public function setStepsCount($value) {
			$this->_numberofstepswalked = $value;
		}
	}
?>
