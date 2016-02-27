<?php

	include 'weeklydata.php';

	$properties = array(
		'user'		=>	$_POST['user'],
		'obsdate'	=>	$_POST['odate'],
		'weight'	=>	$_POST['weight'],
		'waistsize'	=>	$_POST['waist']
	);
	tblweeklydata::create($properties);
	echo '<p>Record has been added</p>';

?>