<?php

	class weeklydata {
		
		protected $_id;
		protected $_userid;
		protected $_obsdate;
		protected $_weight;
		protected $_waistsize;
		
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
		
		public function getWeight() {
			return $this->_weight;
		}
		
		public function setWeight($value) {
			$this->_weight = $value;
		}
		
		public function getWaistSize() {
			return $this->_waistsize;
		}
		
		public function setWaistSize($value) {
			$this->_waistsize = $value;
		}
	}
?>