<?php
	ob_start();
	
	require_once('steps.php');
	
	try {
		if (isset($_GET['id'])) {
			tblstepscounts::delete($_GET['id']);
			$action = 'Record has been deleted.';
		}
		else {
			tblstepscounts::delete();
			$action = 'Records have been deleted.';
		}
	}
	catch(PDOException $e) {
		$action = 'record(s) cannot be deleted.<br>' . $e->getMessage();
	}
	
	header("Location: display_steps.php?action=" . $action);
	
	ob_flush();
?>
