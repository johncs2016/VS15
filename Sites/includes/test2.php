<?php
	
	include "steps.php";
	
	$objects = tblstepscounts::getAll(20);
	echo '<pre>';
	var_dump($objects);
	echo '</pre>';
?>