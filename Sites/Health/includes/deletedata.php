<?php
	ob_start();
	
	require_once('weeklydata.php');
	
	try {
		if (isset($_GET['id'])) {
			tblweeklydata::delete($_GET['id']);
			$action = 'Record has been deleted.';
		}
		else {
			tblweeklydata::delete();
			$action = 'Records have been deleted.';
		}
	}
	catch(PDOException $e) {
		$action = 'record(s) cannot be deleted.<br>' . $e->getMessage();
	}
	
	header("Location: display_data.php?action=" . $action);
	
	ob_flush();
?>
