<?php
	ob_start();
	
	require_once('strength.php');
	
	try {
		if (isset($_GET['id'])) {
			tblstrengthprogs::delete($_GET['id']);
			$action = 'Record has been deleted.';
		}
		else {
			tblstrengthprogs::delete();
			$action = 'Records have been deleted.';
		}
	}
	catch(PDOException $e) {
		$action = 'record(s) cannot be deleted.<br>' . $e->getMessage();
	}
	
	header("Location: display_strength_progs.php?action=" . $action);
	
	ob_flush();
?>