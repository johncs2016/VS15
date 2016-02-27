<?php

	require_once("conversion.php");

	class framesize {

		protected $_id;
		protected $_gender;
		protected $_height;
		protected $_str_height;
		protected $_str_weights;
		protected $_min_weight;
		protected $_max_weight;
		
		public function getID() {
			return $this->_id;
		}
		
		public function setID($value) {
			$this->_id = $value;
		}
		
		public function getGender() {
			return $this->_gender;
		}
		
		public function setGender($value) {
			$this->_gender = $value;
		}

		public function __construct() {
			$this->_str_weights = array(
				'Small'		=> null,
				'Medium'	=> null,
				'Large'		=> null
			);
			$this->_min_weight = array(
				'Small'		=> 0,
				'Medium'	=> 0,
				'Large'		=> 0
			);
			$this->_max_weight = array(
				'Small'		=> 0,
				'Medium'	=> 0,
				'Large'		=> 0
			);
		}
		
		public function getHeight() {
			return $this->_height;
		}

		public function setStrHeight($value) {
			$this->_str_height = $value;
		}
		
		public function setStrWeight($key, $value) {
			$this->_str_weights[$key] = $value;
		}
		
		public function setHeight() {
			$s = trim($this->_str_height);
			$a = explode('.', $s);
			$a[1] = trim($a[1]);
			$a[0] = substr($a[0], 0, strlen($a[0]) - 2);
			$a[1] = substr($a[1], 0, strlen($a[1]) - 2);
			$inches = 12 * (int)($a[0]) + (int)($a[1]);
			$this->_height = conversions::CMfromIN($inches) / 100;
		}
		
		private function setValue($value, $flag) {
			$a = explode('-', $value);
			return (int)($a[$flag]);
		}
		
		public function getMinMaxWeight($key, $flag) {
			$key = substr($key, 0, -6);
			return ($flag ? $this->_min_weight[$key] : $this->_max_weight[$key]);
		}
		
		public function setWeights() {
			foreach ($this->_str_weights as $key => $value) {
				$this->_min_weight[$key] = $this->setValue($value, False);
				$this->_max_weight[$key] = $this->setValue($value, True);
			}
		}
	}
?>