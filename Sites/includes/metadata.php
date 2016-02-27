<?php

	require_once("CRUD.php");
 
 	class tblmetadata extends pdoClass {
		
		protected $_id;
		protected $_userid;
		protected $_tablename;
		protected $_fieldname;
		protected $_description;
		protected $_startdate;
		protected $_enddate;
		
		public function __construct($properties) {
			parent::__construct($properties);
			$this->table = "tblmetadata";
			$this->description = "Metadata";
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

		public function getTablename() {
			return $this->_tablename;
		}
		
		public function setTableName($value) {
			$this->_tablename = $value;
		}

		public function getFieldname() {
			return $this->_fieldname;
		}
		
		public function setFieldName($value) {
			$this->_fieldname = $value;
		}

		public function getDescription() {
			return $this->_description;
		}
		
		public function setDescription($value) {
			$this->_description = $value;
		}

		public function getStartDate() {
		
			$table = $this->getTableName();
			$str_mindate = $table::getMinMaxValue('obsdate', false, false, false) . ' 00:00:00';
			return ($this->_startdate == null ? DateTime::CreateFromFormat('Y-m-d H:i:s', $str_mindate) : $this->_startdate);

		}
		
		public function setStartDate($value) {
			$this->_id = $startdate;
		}

		public function getEndDate() {

			$table = $this->getTableName();
			$str_maxdate = $table::getMinMaxValue('obsdate', false, false, false) . ' 00:00:00';
			return ($this->_enddate == null ? DateTime::CreateFromFormat('Y-m-d H:i:s', $str_maxdate) : $this->_enddate);

		}
		
		public function setEndDate($value) {
			$this->_enddate = $value;
		}

		public function getMinMaxDate($flag) {
			
			$table = $this->getTableName();
			return DateTime::CreateFromFormat('Y-m-d H:i:s', $table::getMaxMinValue('obsdate', false, false, $flag) . ' 00:00:00');
			
		}
	}
?>