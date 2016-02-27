<?php
	class fieldSet {
		private $_label;
		private $_fields;
		
		public function __construct($label = null, $fields = array()) {
			$this->_label = $label;
			$this->_fields = $fields;
		}
		
		public function __toString() {
			$legend = (string)(new legendField($this->_label));
			return ('<fieldset>'.$legend.implode('', $this->_fields).'</fieldset>');
		}
	}