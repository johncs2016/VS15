<?php
	include 'steps.php';

	$root = array(
		'_userid'		=>	$_POST['user'],
		'_obsdate'		=>	$_POST['odate'],
		'_stepscount'	=>	$_POST['steps'],
		'_activityid'	=>	$_POST['activity']
	);
	$object = tblstepscounts::create($root);
	echo '<p>Record has been added</p>';
?>