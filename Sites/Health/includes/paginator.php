<?php
	class Paginator {
		
		protected $items_per_page;
		protected $items_total;
		protected $current_page;
		protected $num_pages;
		protected $mid_range;
		protected $low;
		protected $high;
		protected $limit;
		protected $ret;
		protected $default_ipp = 25;
		
		public function __get($name) {
			return $this->$name;
		}
		
		public function __set($name, $value) {
			$this->$name = $value;
		}
		
		public function __construct() {
			$this->current_page = 1;
			$this->mid_range = 7;
			$this->items_per_page = (!empty($_GET['ipp'])) ? $_GET['ipp'] : $this->default_ipp;
		}
		
		public function paginate() {
			if($_GET['ipp'] == 'All') {
				$this->num_pages = ceil($this->items_total / $this->default_ipp);
				$this->items_per_page = $this->default_ipp;
			}
			else {
				if (!is_numeric($this->items_per_page) || $this->items_per_page <= 0) {
					$this->items_per_page = $this->default_ipp;
				}
				$this->num_pages = ceil($this->items_total / $this->items_per_page);
			}
			$this->current_page = (int) $_GET['page'];
			if ($this->current_page < 1 || !is_numeric($this->current_page)) {
				$this->current_page = 1;
			}
			if ($this->current_page > $this->num_pages) {
				$this->current_page = $this->num_pages;
			}
			$prev_page = $this->current_page - 1;
			$next_page = $this->current_page + 1;
			if ($this->num_pages > 10) {
				$this->ret = ($this->current_page != 1 && $this->items_total <= 10) ?
					"<a class=\"paginate\" href=\"$_SERVER[PHP_SELF]?page=$prev_page&ipp=$this->items_per_page\">Previous</a>" :
					"<span class=\"inactive\"href=\"#\">Previous</span>";
				$this->start_range = $this->current_page - floor($this->mid_range / 2);
				$this->end_range = $this->current_page + floor($this->mid_range / 2);
				if ($this->start_range <= 0) {
					$this->end_range += abs($this->start_range) + 1;
					$this->start_range = 1;
				}
				if ($this->end_range > $this->num_pages) {
					$this->start_range -= $this->end_range - $this->num_pages;
					$this->end_range = $this->num_pages;
				}
				$this->range = range($this->start_range,$this->end_range);
				for ($i = 1; $i <= $this->num_pages; $i++) {
					if ($this->range[0] > 2 && $i == $this->range[0]) {
						$this->ret .= " ... ";
					}
					if ($i == 1 || $i == $this->num_pages || in_array($i, $this->range)) {
						$this->ret .= ($i == $this->current_page && $_GET['page'] != 'All') ?
							"<a title=\"Go to page $i of $this->num_pages\" class=\"current\" href=\"#\">$i</a>" :
							"<a class=\"paginate\" title=\"Go to page $i of $this->num_pages\" href=\"$_SERVER[PHP_SELF]?page=$i&ipp=$this->items_per_page\">$i</a> ";
					}
					if ($this->range[$this->mid_range - 1] < $this->num_pages - 1 || $i == $this->range[$this->mid_range - 1]) {
						$this->ret = " ... ";
					}
				}
				$this->ret .= (($this->current_page != $this->num_pages && $this->items_total >= 10) && ($_GET['page'] != 'All')) ?
					"<a class=\"paginate\" href=\"$_SERVER[PHP_SELF]?page=$next_page&ipp=$this->items_per_page\">Next</a>\n" :
					"<span class=\"inactive\" href=\"#\">Next</span>\n";
				$this->ret .= ($_GET['page'] == 'All') ?
					"<a class=\"current\" style=\"margin-left:10px\" href=\"#\">All</a> \n" :
					"<a class=\"paginate\" style=\"margin-left:10px\" href=\"$_SERVER[PHP_SELF]?page=1&ipp=All</a> \n";
			}
			else {
				for ($i = 1; $i <= $this->num_pages; $i++) {
					$this->ret .= ($i == $this->current_page) ?
						"<a class=\"current\" href=\"#\">$i</a> " :
						"<a class=\"paginate\" href=\"$_SERVER[PHP_SELF]?page=$i&ipp=$this->items_per_page\">$i</a> ";
				}
				$this->ret .= "<a class=\"paginate\" href=\"$_SERVER[PHP_SELF]?page=1&ipp=All\">All</a> \n";
			}
			$this->low = ($this->current_page - 1) * $this->items_per_page;
			$this->high = ($_GET['ipp'] == 'All') ? $this->items_total : ($this->current_page * $this->items_per_page) - 1;
			$this->limit = ($_GET['ipp'] == 'All') ? "" : " LIMIT $this->low, $this->items_per_page"; 
		}

		public function display_items_per_page() {
			$items = "";
			$ipp_array = array(10,25,50,100,'All');
			foreach ($ipp_array as $ipp_opt) {
				$items .= ($ipp_opt == $this->items_per_page) ?
					"<option selected value=\"$ipp_opt\">$ipp_opt</option>\n" :
					"<option value=\"$ipp_opt\">$ipp_opt</option>\n";
			}
			return "<span class=\"paginate\">Items per page: </span><select class=\"paginate\" onchange=\"window.location='$_SERVER[PHP_SELF]?page=1&ipp='+this[this.selectedIndex].value;return false\">$items</select>\n";
		}
		
		public function display_jump_menu() {
			for ($i = 1; $I <= $this->num_pages; $i++) {
				$option .= ($i == $this->current_page) ?
					"<option value=\"$i\" selected>$i</option>\n" :
					"<option value=\"$i\">$i</option>\n";
			}
			return "<span class=\"paginate\">Page:</span><select class=\"paginate\" onchange=\"window.location='$_SERVER[PHP_SELF]?page='+this[this.selectedIndex].value+'&ipp=$this->items_per_page';return false\">$option</select>\n";
		}
		
		public function display_pages() {
			return $this->ret;
		}
	}
?>
