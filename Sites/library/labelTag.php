<?php
	class labelTag {
		
		private $_name, $_text;
		
		public function __construct($name, $text) {
			$this->_name = $name;
			$this->_text = $text;
		}
		
		public function __toString() {
			if ($this->_name == null || $this->_name == '') {
				return null;
			} else {
				return "<label for='" . $this->_name . "'>" . $this->_text . "</label>";
			}
		}
	}