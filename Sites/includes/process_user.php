<?php
	ob_start();
	
	include 'user.php';
	
	$action = (isset($_GET['id']) ? 'updated' : 'added');

	try {
		$root = array(
			'_firstname'		=>	$_POST['first'],
			'_lastname'			=>	$_POST['last'],
			'_emailaddress'		=>	$_POST['gender'],
			'_genderid'			=>	$_POST['email'],
			'_dob'				=>	$_POST['dob'],
			'_height'			=>	$_POST['height'],
			'_stepsize'			=>	$_POST['step'],
			'_wristsize'		=>	$_POST['wrist'],
			'_forearmsize'		=>	$_POST['forearm'],
			'_hipsize'			=>	$_POST['hip'],
			'_isimperial'		=>	isset($_POST['isimperial']) ? $_POST['isimperial'] : 0,
			'_twindexid'		=>	$_POST['twindex']
		);
		if(isset($_GET['id'])) $root['_id'] = $_GET['id'];
		$object = tblusers::create($root);
		$action = 'record has been ' . $action . '.';
	}
	catch (PDOException $e) {
		$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
	}
	header("Location: display_users.php?action=" . $action);
	
	ob_flush();
?>