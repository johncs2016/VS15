<?php
	class bloodPressure {
		private $_id;
		private $_systolic;
		private $_diastolic;
		private $_pulse;

		public function __construct($systolic, $diastolic, $pulse = null, $id = null) {
			$this->setSystolic($systolic);
			$this->setDiastolic($diastolic);
			$this->setPulse($pulse);
			$this->setID($id);
		}

		public function __tostring() {
			return $this->display();
		}

		public function display() {
			return number_format($this->getSystolic()) . " over " . number_format($this->getDiastolic());
		}

		public function  getID() {
			return $this->_id;
		}

		public function setID($id) {
			$this->_id = $id;
		}

		public function  getPulse() {
			return $this->_pulse;
		}

		public function setPulse($id) {
			$this->_pulse = $id;
		}

		public function getSystolic() {
			return $this->_systolic;
		}

		public function setSystolic($systolic) {
			$this->_systolic = $systolic;
		}

		public function getDiastolic() {
			return $this->_diastolic;
		}

		public function setDiastolic($diastolic) {
			$this->_diastolic = $diastolic;
		}

		public function getCategory() {

			$sys = $this->getSystolic();
			$dia = $this->getDiastolic();

			if($sys == null || $dia == null || $sys == 0 || $dia == 0) return null;
			if($sys >= 160) return ($dia >= 90 ? "Hypertension Stage 2" : "Isolated Systolic Hypertension");
			if($sys >= 140) return ($dia >= 90 ? "hypertension Stage " . ($dia >= 100 ? 2 : 1) : "Isolated Systolic Hypertension");
			if($sys >= 120) return ($dia >= 90 ? "Hypertension Stage " . ($dia >= 100 ? 2 : 1) : "pre-High Blood Pressure");
			if($sys >= 90) return ($dia >= 90 ? "Hypertension Stage " . ($dia >= 100 ? 2 : 1) : ($dia >= 80 ? "pre-High Blood Pressure" : "ideal Blood Pressure"));
			return ($dia >= 90 ? "Hypertension Stage " . ($dia >= 100 ? 2 : 1) : ($dia >= 80 ? "pre-High Blood Pressure" : ($dia >= 60 ? "ideal Blood Pressure" : "Low Blood Pressure")));
		}
		
		public function getPulsePressure() {
			return $this->getSystolic() - $this->getDiastolic();
		}
		
		public function getMAP() {  // Mean Arterial Pressure
			return $this->getDiastolic() - ($this->getPulsePressure() / 3);
		}
		
		public function getBPFactor() {
			return $this->getSystolic() + $this->getDiastolic();
		}
	}
?>