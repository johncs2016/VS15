<?php
	ob_start();
	
	require_once('exercise.php');
	
	try {
		if (isset($_GET['id'])) {
			tblexercise::delete($_GET['id']);
			$action = 'Record has been deleted.';
		}
		else {
			tblexercise::delete();
			$action = 'Records have been deleted.';
		}
	}
	catch(PDOException $e) {
		$action = 'record(s) cannot be deleted.<br>' . $e->getMessage();
	}
	
	header("Location: display_exercise.php?action=" . $action);
	
	ob_flush();
?>