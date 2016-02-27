<?php
	
	require_once("CRUD.php");
	require_once("user.php");
	require_once("blood_pressure.php");
	
	class tblmedical extends pdoClass {

		protected $_id;
		protected $_userid;
		protected $_obsdate;
		protected $_systolic;
		protected $_diastolic;
		protected $_pulse;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblmedical";
			$this->description = "Blood Pressure Readings";
			$this->bp_object = $this->getBloodPressure();
		}
		
		public function __tostring() {
			return $this->bp_object->display();
		}

		public function getBloodPressure() {
			return new bloodPressure($this->getSystolic(), $this->getDiastolic(), $this->getPulse(), $this->getID());
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

		public function getSystolic() {
			return $this->_systolic;
		}
		
		public function setSystolic($value) {
			$this->_systolic = $value;
		}

		public function getDiastolic() {
			return $this->_diastolic;
		}
		
		public function setDiastolic($value) {
			$this->_diastolic = $value;
		}

		public function getPulse() {
			return $this->_pulse;
		}
		
		public function setPulse($value) {
			$this->_pulse = $value;
		}

		public function pulse_pressure() {
			return $this->bp_object->getPulsePressure();
		}
		
		public function bp_category() {
			return $this->bp_object->getCategory();
		}
		
		public function MAP() {
			return $this->bp_object->getMAP();
		}
		
		public function BPFactor() {
			return $this->bp_object->getBPFactor();
		}
	}
?>
