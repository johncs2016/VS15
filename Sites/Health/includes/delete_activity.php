<?php
	ob_start();
	
	require_once('activities.php');
	
	try {
		if (isset($_GET['id'])) {
			tblactivities::delete($_GET['id']);
			$action = 'Record has been deleted.';
		}
		else {
			tblactivities::delete();
			$action = 'Records have been deleted.';
		}
	}
	catch(PDOException $e) {
		$action = 'record(s) cannot be deleted.<br>' . $e->getMessage();
	}
	
	header("Location: display_activities.php?action=" . $action);
	
	ob_flush();
?>
