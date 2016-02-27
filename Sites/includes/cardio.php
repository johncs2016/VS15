<?php

	require_once("cardiouserprogs.php");

	class tblcardio extends tblcardiouserprogs {
			
		protected $_obsdate;
		protected $_venueid;
		protected $_distance;
		protected $_calories;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblcardio";
			$this->description = "Cardio Exercises";
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

		public function getDistance() {
			return ($this->getEquipmentID() != 1 ? $this->_distance : $this->getSpeed() * $this->getMinutes() / 60);
		}
		
		public function setDistance($value) {
			$this->_distance = $value;
		}

		public function getCalories() {
			return $this->_calories;
		}
		
		public function setCalories($value) {
			$this->_calories = $value;
		}
	}
?>
