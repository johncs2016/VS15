<?php

	require_once("CRUD.php");

	class tblvenues extends pdoClass {
			
		protected $_id;
		protected $_venue;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblvenues";
			$this->description = "Activity Venues";
		}

		public function getID() {
			return $this->_id;
		}
		
		public function setID($value) {
			$this->_id = $value;
		}

		public function getVenue() {
			return $this->_venue;
		}
		
		public function setVenue($value) {
			$this->_venue = $value;
		}
	}
?>
