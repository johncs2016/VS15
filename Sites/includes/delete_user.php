<?php
	ob_start();
	
	require_once('user.php');
	
	try {
		if (isset($_GET['id'])) {
			tblusers::delete($_GET['id']);
			$action = 'Record has been deleted.';
		}
		else {
			tblusers::delete();
			$action = 'Records have been deleted.';
		}
	}
	catch(PDOException $e) {
		$action = 'record(s) cannot be deleted.<br>' . $e->getMessage();
	}
	
	header("Location: display_users.php?action=" . $action);
	
	ob_flush();
?>
