<?php

	require_once("CRUD.php");

	class tbldetails extends pdoClass {

		protected $_id;
		protected $_firstname;
		protected $_lastname;
		protected $_genderid;
		protected $_emailaddress;
		protected $_dob;
		
		public function __construct(array $properties) {
			parent::__construct($properties);
			$this->table = "tbldetails";
			$this->description = "Details";
		}

		public function getID() {
			return $this->_id;
		}
	
		public function setID($value) {
			$this->_id = $value;
		}
	
		public function fullname() {
			return $this->_firstname . ' ' . $this->_lastname;
		}
		
		public function getFirstName() {
			return $this->_firstname;
		}
		
		public function setFirstName($value) {
			$this->_firstname = $value;
		}

		public function getLastName() {
			return $this->_lastname;
		}
		
		public function setLastName($value) {
			$this->_last_name = $value;
		}

		public function getGenderID() {
			return $this->_genderid;
		}
	
		public function setGenderID($value) {
			$this->_genderid = $value;
		}
	
		public function getEmailAddress() {
			return $this->_emailaddress;
		}
		
		public function setEmailAddress($value) {
			$this->_emailaddress = $value;
		}

		public function getDOB() {
			return $this->_dob;
		}
		
		public function setDOB($value) {
			$this->_dob = $value;
		}

		public function age() {
			if($this->_dob != null) {
				$now = new DateTime();
				$birth = $this->_dob;
				return $birth->diff($now)->format('%r%y');
			} else {
				return false;
			}
		}
		
		public function __tostring() {
			return $this->fullname();
		}
		
		public function isMale() {
			return ($this->_genderid == 1);
		}

		public function getRecipient() {
			return htmlentities($this->fullname() . ' <' . $this->getEmailAddress() . '>');
		}
	}
?>
