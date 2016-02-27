<?php

	require_once("CRUD.php");

	class tblactivities extends pdoClass {
		
		protected $_id;
		protected $_userid;
		protected $_activity;
		protected $_show_activity;
		protected $_met;
		protected $_duration;
		protected $_equivalentsteps;
	
		public function __construct(array $properties) {
			parent::__construct($properties);
			$this->table = "tblactivities";
			$this->description = "Activities";
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
	
		public function getActivity() {
			return $this->_activity;
		}
	
		public function setActivity($value) {
			$this->_activity = $value;
		}
	
		public function getShowActivity() {
			return ($this->_show_activity == 0 ? false : true);
		}
	
		public function setShowActivity($value) {
			$this->_show_activity = ($value == true ? 1 : 0);
		}
	
		public function getMET() {
			return $this->_met;
		}
		
		public function setMET($value) {
			$this->_met = $value;
		}

		public function getDuration() {
			return $this->_duration;
		}
		
		public function setDuration($value) {
			$this->_duration = $value;
		}

		public function getEquivalentSteps() {
			return $this->_equivalentsteps;
		}
		
		public function setEquivalentSteps($value) {
			$this->_equivalentsteps = $value;
		}
	}
?>
