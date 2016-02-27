<?php

	require_once 'CRUD.php';
	require_once 'user.php';

	class tblweeklydata extends pdoClass {
		
		protected $_id;
		protected $_userid;
		protected $_obsdate;
        protected $_weight_flag;
        protected $_waist_flag;
		protected $_weight;
		protected $_waistsize;

		public $isImperial;
		
		public $description = array(1 => 'Weight', 'Waist Size');
		public $canvas = array(1 => "cvsWeight",2 => "cvsWaist");

		public function getUnits() {
			return array(1 => ($this->isImperial ? "Stone" : "kg"), 2 => ($this->isImperial ? "in" : "cm"));
		}
		
		public function __construct(array $properties) {
			parent::__construct($properties);
			$obj = tblusers::get($this->_userid);
			$this->isImperial = $obj->getIsImperial();
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
		

                public function getWeightFlag() {
                    return $this->_weight_flag;
                }
                
                public function setWeightFlag($flag = false) {
                    $this->_weight_flag = $flag;
                }
                
                public function getWaistFlag() {
                    return $this->_waist_flag;
                }
                
                public function setWaistFlag($flag = false) {
                    $this->_waist_flag = $flag;
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
