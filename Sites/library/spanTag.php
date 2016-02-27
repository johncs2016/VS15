<?php
	class spanTag {
	
		private $_id, $_class, $_text;
		
		public function __construct($id, $class, $text) {
			$this->_id = $id;
			$this->_class = $class;
			$this->_text = $text;
		}
		
		public function __toString() {
			$tag = "<span";
			$tag .= ($this->_id != null) ? " id='{$this->_id}'" : "";
			$tag .= ($this->_class != null) ? " class='{$this->_class}'" : "";
			$tag .= ">" . $this->_text . "</span>";
	
			return $tag;
		}
		
		
	}
?>
