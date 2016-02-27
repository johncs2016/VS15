<?php
	class form {
		private $_id,
				$_class,
				$_name,
				$_action,
				$_method,
				$_fields;

		public function __construct($id = null, $class = null, $name = null, $action = null, $method = true, $fields = array()) {
			$this->_id = $id;
			$this->_class = $class;
			$this->_name = $name;
			$this->_action = $action;
			$this->_method = $method;
			$this->_fields = $fields;
		}
		
		public function __toString() {

			$tag = "<form";
			$tag .= ($this->_id == null) ? "" : " id='" . $this->_id . "'";
			$tag .= ($this->_class == null) ? "" : " class='" . $this->_id . "'";
			$tag .= ($this->_name == null) ? "" : " name='" . $this->_id . "'";
			$tag .= " action='" . (($this->_action == null) ? "'" : $this->_action) . "'";
			$tag .= " method='" . (($this->_method == true) ? "POST" : "GET") . "'>";

			return ($tag . implode("", $this->_fields) . "</form>");
		}
	}