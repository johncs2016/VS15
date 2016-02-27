<?php

	require_once("CRUD.php");

	class tblexercise extends pdoClass {
			
		protected $_id;
		protected $_userid;
		protected $_obsdate;
		protected $_venueid;
		protected $_activityid;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblexercise";
			$this->description = "Daily Activities";
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

		public function getVenueID() {
			return $this->_venueid;
		}
		
		public function setVenueID($value) {
			$this->_venueid = $value;
		}

		public function getActivityID() {
			return $this->_activityid;
		}
		
		public function setActivityID($value) {
			$this->_activityid = $value;
		}
	}
?>
