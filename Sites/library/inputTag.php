<?php
	class InputTag {
		
		private $_type,
				$_id,
				$_class,
				$_style,
				$_value,
				$_attributes = array(),
				$_possible_types = array(
								'Email', 'Tel', 'Url', 'Search', 'Color', 'Number', 'Range', 'Date', 'Text', 'Button', 'Hidden',
								'Password', 'Checkbox', 'Radio', 'File', 'Submit', 'Reset'
				),
				$_possible_attributes = array(
								'Placeholder', 'Autofocus', 'Maxlength', 'List', 'Autocomplete', 'Required', 'Pattern', 'Spellcheck',
								'Novalidate', 'Formnovalidate', 'Formmethod', 'Formtarget', 'Formenctype', 'minlength', 'readonly',
								'disabled', 'size', 'name', 'height', 'width', 'source', 'checked', 'tabindex', 'form', 'inputmode',
								'dirname', 'Accept', 'Multiple', 'Min', 'Max', 'Step'
				);
								
		private function valid_type($type) {
			return (in_array($type, $this->_possible_types) ? true : false);
		}
		
		private function valid_attribute($attribute) {
			return (in_array($attribute, $this->_possible_attributes) ? true : false);
		}
		
		public function __construct($type, $id = null, $class = null, $style = null, $value = null, $attributes = array()) {
			$this->_type = ($this->valid_type($type) ? $type : null);
			$this->_id = $id;
			$this->_class = $class;
			$this->_style = $style;
			$this->_value = $value;
			foreach ($attributes as $key => $value) {
				if($this->valid_attribute($key)) {
					if ($value != null && $value != '') {
						$this->_attributes[$key] = $value;
					}
				}
			}
		}
		
		public function __toString() {
			if($this->_type == null) {
				return null;
			} else {
				$tag = "<input type='{$this->_type}'";
				$tag .= ($this->_id != null) ? " id='{$this->_id}'" : "";
				$tag .= ($this->_class != null) ? " class='{$this->_class}'" : "";
				$tag .= ($this->_style != null) ? " style='{$this->_style}'" : "";
				$tag .= " value='".(($this->_value != null) ? "{$this->_value}'" : "'");
				foreach($this->_attributes as $key => $value) {
					if (in_array($key, array('Autofocus', 'checked', 'disabled', 'Required', 'readonly'))) {
						if ($value == true) {
							$tag .= " {$key}";
						}
					} else {
						$tag .= " {$key}='{$value}'";
					}
				}
				return $tag.">";
			}
		}
	}
?>