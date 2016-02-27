<?php
	ob_start();
	
	include 'medical.php';
	
	$action = (isset($_GET['id']) ? 'updated' : 'added');

	try {
		$medical_records = array(
			'_userid'		=>	$_POST['user'],
			'_obsdate'		=>	$_POST['odate'],
			'_systolic'		=>	$_POST['systolic'],
			'_diastolic'	=>	$_POST['diastolic'],
			'_pulse'		=>	$_POST['pulse']
		);
		if(isset($id)) $medical_records['_id'] = $_GET['id'];
		$object = tblmedical::create($medical_records);
		$action = 'record has been ' . $action . '.';
	}
	catch (PDOException $e) {
		$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
	}
	header("Location: display_medical.php?action=" . $action);
	
	ob_flush();
?>