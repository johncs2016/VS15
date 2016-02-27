<?php
	
	require_once "CRUD.php";
	
	class tblreportrecipients extends pdoClass {
		
		protected $_id;
		protected $_recid;
		protected $_detailsid;
		protected $_userid;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblreportrecipients";
			$this->description = "Report Recipients";
		}
		
		public function getID() {
			return $this->_id;
		}
		
		public function setID($value) {
			$this->_id = $value;
		}

		public function getRecID() {
			return $this->_recid;
		}
		
		public function setRecID($value) {
			$this->_recid = $value;
		}
		
		public function getDetailsID() {
			return $this->_detailsid;
		}
		
		public function setDetailsID($value) {
			$this->_detailsid = $value;
		}

		public function getUserID() {
			return $this->_userid;
		}
		
		public function setUserID($value) {
			$this->_userid = $value;
		}

	}
?>
