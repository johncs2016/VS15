<?php

	require_once("strengthequipment.php");

	class tblcardioequipment extends tblstrengthequipment {
			
		protected $_km;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblcardioequipment";
			$this->description = "Cardio Equipment Details";
		}

		public function getKM() {
			return ($this->_km == 1 ? true : false);
		}
		
		public function setKM($value) {
			$this->_km = ($value == true ? 1 : 0);
		}
	}
?>
