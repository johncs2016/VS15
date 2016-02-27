<?php
	ob_start();
	
	include 'activities.php';
	
	$action = (isset($_GET['id']) ? 'updated' : 'added');

	try {
		$root = array(
			'_activity'		=>	$_POST['activity'],
			'_showrecord'	=>	isset($_POST['show']) ? $_POST['show'] : 0
		);
		if(isset($_GET['id'])) $root['_id'] = $_GET['id'];
		$object = tblactivities::create($root);
		$action = 'record has been ' . $action . '.';
	}
	catch (PDOException $e) {
		$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
	}
	header("Location: display_activities.php?action=" . $action);
	
	ob_flush();
?>

