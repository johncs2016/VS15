<?php

	require_once("CRUD.php");
 
 	class tblusers extends pdoClass {
		
		protected $_id;
		protected $_username;
		protected $_password;
		protected $_sessionid;
		protected $_dateregistered;
		protected $_detailsid;
		protected $_isimperial;
		protected $_twindexid;
		protected $_targetcalories;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblusers";
			$this->description = "Users";
		}
		
		public function getID() {
			return $this->_id;
		}
		
		public function setID($value) {
			$this->_id = $value;
		}

		public function getUsername() {
			return $this->_username;
		}
		
		public function setUsername($value) {
			$this->_username = $value;
		}

		public function getPassword() {
			return $this->_password;
		}
		
		public function setPassword($value) {
			$this->_password = md5($value);
		}

		public function getSessionID() {
			return $this->_sessionid;
		}
		
		public function setSessionID($value) {
			$this->_sessionid = $value;
		}

		public function getDateRegistered() {
			return $this->_dateregistered;
		}
		
		public function setDateRegistered($value) {
			$this->_DateRegistered = $value;
		}

		public function getDetailsID() {
			return $this->_detailsid;
		}
		
		public function setDetailsID($value) {
			$this->_detailsid = $value;
		}

		public function getIsImperial() {
			return $this->_isimperial;
		}
		
		public function setIsImperial($value) {
			$this->_isimperial = $value;
		}

		public function getTWIndexid() {
			return $this->_twindexid;
		}
		
		public function setTWIndexid($value) {
			$this->_twindexid = $value;
		}
		
		public function getTargetCalories() {
			return $this->_targetcalories;
		}
		
		public function setTargetCalories($value) {
			$this->_targetcalories = $value;
		}

		public function __tostring() {
			return tbldetails::get($this->_detailsid)->fullname();
		}
	}
?>
