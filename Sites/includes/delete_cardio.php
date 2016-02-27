<?php
	ob_start();
	
	require_once('cardio.php');
	
	try {
		if (isset($_GET['id'])) {
			tblcardio::delete($_GET['id']);
			$action = 'Record has been deleted.';
		}
		else {
			tblcardio::delete();
			$action = 'Records have been deleted.';
		}
	}
	catch(PDOException $e) {
		$action = 'record(s) cannot be deleted.<br>' . $e->getMessage();
	}
	
	header("Location: display_cardio.php?action=" . $action);
	
	ob_flush();
?>