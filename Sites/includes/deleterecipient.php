<?php
	ob_start();
	
	require_once('recipient.php');
	
	try {
		if (isset($_GET['id'])) {
			tblreportrecipients::delete($_GET['id']);
			$action = 'Record has been deleted.';
		}
		else {
			tblreportrecipients::delete();
			$action = 'Records have been deleted.';
		}
	}
	catch(PDOException $e) {
		$action = 'record(s) cannot be deleted.<br>' . $e->getMessage();
	}
	
	header("Location: report.php?action=" . $action);
	
	ob_flush();
?>
