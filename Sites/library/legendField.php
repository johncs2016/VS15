<?php
	class legendField {
		private $_label;
		
		public function __construct($label = null) {
			$this->_label = $label;
		}
		
		public function __toString() {
			return '<legend>'.$this->_label.'</legend>';
		}
	}
?>