<?php
	class PTag {
		private $_text;
		
		public function __construct($text) {
			$this->_text = $text;
		}
		
		public function __toString() {
			return '<p>'.$this->_text.'</p>';
		}
	}