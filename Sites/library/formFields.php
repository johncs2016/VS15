<?php
	class formFields {
		
		private $_name, $_label, $value;
		
		public function __construct($name, $label, $value = null) {
			$this->_name = $name;
			$this->_label = $label;
			$this->_value = $value;
		}
		
		public function passwordField() {
			$password_text_box = new inputTag("Password", $this->_name, null, null, $this->_value, array('name' => $this->_name));
			$password_label = new labelTag($this->_name, $this->_label);
			return (string)$password_label . (string)$password_text_box;
		}
		
		public function textField() {
			$textbox = new InputTag("Text", null, null, null, $this->_value, array("name" => $this->_name));
			$label = new labelTag($this->_name, $this->_label);
			return (string)$label . (string)$textbox;
		}
		
		public function checkBoxField() {
			$checkbox = new InputTag("Checkbox", null, null, null, $this->_value, array("name" => $this->_name));
			$label = new labelTag($this->_name, (string)$checkbox . $this->_label);
			return (string)$label;
		}
	}
?>