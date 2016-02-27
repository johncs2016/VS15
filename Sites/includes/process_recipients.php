<?php
	ob_start();
	
	include 'recipient.php';
	
	$action = (isset($_GET['id']) ? 'updated' : 'added');

	try {
		$root = array(
			'_userid'		=>	$_POST['user'],
			'_firstname'	=>	$_POST['first'],
			'_lastname'		=>	$_POST['last'],
			'_emailaddress'	=>	$_POST['email']
		);
		if(isset($id)) $root['_id'] = $_GET['id'];
		$object = tblreportrecipients::create($root);
		$action = 'record has been ' . $action . '.';
	}
	catch (PDOException $e) {
		$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
	}
	header("Location: display_recipients.php?action=" . $action);
	
	ob_flush();
?>