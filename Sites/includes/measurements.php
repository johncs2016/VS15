<?php
	require_once("conversion.php");
	require_once("typesofmeasurements.php");

	abstract class measurement extends conversions {

		protected $_value;
		protected $_isimp;
		protected $_type;
		protected $_units;

		public function __construct($value, $type, $isimp) {
			$this->setValue($value);
			$this->setIsImp($isimp);
			$this->setType($type);
			$this->setUnits();
		}

		public function getValue() {
			return $this->_value;
		}

		public function setValue($value) {
			$this->_value = $value;
		}

		protected function getIsImp() {
			return $this->_isimp;
		}

		protected function setIsImp($isimp) {
			$this->_isimp = $isimp;
		}

		protected function getType() {
			return $this->_type;
		}

		protected function setType($type) {
			$this->_type = $type;
		}

		protected function getUnits() {
			return $this->_units;
		}

		protected function setUnits() {
			$arr = array();
			$obj = tbltypesofmeasurements::get($this->getType());
			if (!$this->getIsImp()) {
				$arr[] = $obj->getUnits();
			} else {
				$arr[] = $obj->getImpUnits1();
				if ($obj->getImpUnits2() !== null) {
					$arr[] = $obj->getImpUnits2();
				}
			}
			$this->_units = $arr;
		}

		public function getCount() {
			return count($this->getUnits());
		}

		abstract public function display();

		public function __tostring() {
			return $this->display();
		}
	}

	class weight extends measurement {

		public function __construct($value, $type, $isimp) {
			parent::__construct($value, $type, $isimp);
		}

		public function getPounds() {
			return self::totPOUNDfromKG($this->_value);
		}

		public function getStones() {
			return self::STONEfromKG($this->_value);
		}

		protected function getIntPounds() {
			return self::intPOUNDfromKG($this->_value);
		}

		protected function getIntStones() {
			return self::intSTONEfromKG($this->_value);
		}

		public function display() {
			$units = $this->getUnits();

			if($this->getCount() == 2) {
				return number_format($this->getIntStones()) . $units[0] . ' ' . number_format($this->getIntPounds()) . $units[1];
			} else {
				return number_format($this->getValue(), 1) . $units[0];
			}
		}
	}

	class height extends measurement {

		public function __construct($value, $type, $isimp) {
			parent::__construct($value, $type, $isimp);
		}

		public function getInches() {
			return self::MtoIN($this->_value);
		}

		public function getFeet() {
			return self::MtoFEET($this->_value);
		}

		protected function getIntInches() {
			return self::intINfromM($this->_value);
		}

		protected function getIntFeet() {
			return self::intFTfromM($this->_value);
		}

		public function display() {
			$units = $this->getUnits();
			if($this->getCount() == 2) {
				return number_format($this->getIntFeet()) . $units[0] . ' ' . number_format($this->getIntInches()) . $units[1];
			} else {
				return number_format($this->getValue(), 1) . $units[0];
			}

		}
	}

	class distance extends measurement {
		public function __construct($value, $type, $isimp) {
			parent::__construct($value, $type, $isimp);
		}

		public function getMiles() {
			return self::MILEfromKM($this->_value);
		}

		public function display() {
			$units = $this->getUnits();
			return number_format(($this->_isimp ? $this->getMiles() : $this->getValue()), 1) . $units[0];
		}
	}

	class size extends measurement {
		public function __construct($value, $type, $isimp) {
			parent::__construct($value, $type, $isimp);
		}

		public function getInches() {
			return self::INfromCM($this->_value);
		}

		public function display() {
			$units = $this->getUnits();
			return number_format(($this->_isimp ? $this->getInches() : $this->getValue()), 1) . $units[0];
		}
	}
?>
