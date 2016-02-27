<?php

	require_once("user.php");
	require_once("basic.php");

	class bodyMeasurements {
		
		private $_userid;
		private $_height;
		private $_stepsize;
		private $_wristsize;
		private $_forearmsize;
		private $_hipsize;
		
		public function __construct($userid) {
			$this->setUserID($userid);
			$this->setValue("height");
			$this->setValue("stepsize");
			$this->setValue("wristsize");
			$this->setvalue("forearmsize");
			$this->setValue("hipsize");
		}

		public function getUserID() {
			return $this->_userid;
		}
		
		public function setUserID($value) {
			$this->_userid = $value;
		}

		public function getHeight() {
			return $this->_height;
		}
		
		public function setHeight($value) {
			$this->_height = $value;
		}

		public function getWristSize() {
			return $this->_wristsize;
		}
		
		public function setWristSize($value) {
			$this->_wristsize = $value;
		}

		public function getForearmSize() {
			return $this->_forearmsize;
		}
		
		public function setForearmSize($value) {
			$this->_forearmsize = $value;
		}

		public function getStepSize() {
			return $this->_stepsize;
		}
		
		public function setStepSize($value) {
			$this->_stepsize = $value;
		}

		public function getHipSize() {
			return $this->_hipsize;
		}
		
		public function setHipSize($value) {
			$this->_hipsize = $value;
		}

		private function setValue($name) {
			$userid = $this->getUserID();
			$property = "_" . $name;
			$sql = "SELECT value FROM tblbasicmeasurements WHERE name = :name AND userid = :userid LIMIT 1";
			try {
				$db = tblbasicmeasurements::getConnection();
				$query = $db->prepare($sql);
				$query->bindparam(':name', $name, PDO::PARAM_STR);
				$query->bindparam(':userid', $userid, PDO::PARAM_INT);
				$query->execute();
				$row = $query->fetch(PDO::FETCH_ASSOC);
				$this->$property = (empty($row) ? 0 : $row['value']);
			}
			catch (PDOException $e) {
				$e->getMessage();
			}

		}
	}
?>
