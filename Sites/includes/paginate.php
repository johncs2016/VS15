<?php
	require_once('CRUD.php');
	
	class paginate extends pdoClass {
		protected $target;
		protected $tot_recs;
		protected $limit;
		protected $pages;
		protected $page;
		
	}
?>