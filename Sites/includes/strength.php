<?php

	require_once("strengthprogs.php");

	class tblstrength extends tblstrengthprogs {
			
		protected $_obsdate;
		protected $_venueid;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblstrength";
			$this->description = "Strength Exercises";
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
	}
?>
