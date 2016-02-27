<?php
	ob_start();
	
	require_once('medical.php');

	try {
		if (isset($_GET['id'])) {
			tblmedical::delete($_GET['id']);
			$action = 'Record has been deleted.';
		}
		else {
			tblmedical::delete();
			$action = 'Records have been deleted.';
		}
	}
	catch(PDOException $e) {
		$action = 'record(s) cannot be deleted.<br>' . $e->getMessage();
	}
	
	header("Location: display_medical.php?action=" . $action);
	
	ob_flush();
?>
