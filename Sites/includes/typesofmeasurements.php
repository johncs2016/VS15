<?php

	require_once("CRUD.php");

	class tbltypesofmeasurements extends pdoClass {
		
		protected $_id;
		protected $_type;
		protected $_units;
		protected $_impunits1;
		protected $_impunits2;
	
		public function __construct(array $properties) {
			parent::__construct($properties);
			$this->table = "tbltypesofmeasurements";
			$this->description = "Types Of Measurements";
		}

		public function getID() {
			return $this->_id;
		}
	
		public function setID($value) {
			$this->_id = $value;
		}
	
		public function getType() {
			return $this->_type;
		}
	
		public function setType($value) {
			$this->_type = $value;
		}
	
		public function getUnits() {
			return $this->_units;
		}
	
		public function setUnits($value) {
			$this->_units = $value;
		}
	
		public function getImpUnits1() {
			return $this->_impunits1;
		}
	
		public function setImpUnits1($value) {
			$this->_impunits1 = $value;
		}
	
		public function getImpUnits2() {
			return $this->_impunits2;
		}
	
		public function setImpUnits2($value) {
			$this->_impunits2 = $value;
		}

		public function getMultiple() {
			return ($this->impunits2 !== null);
		}
	}
?>