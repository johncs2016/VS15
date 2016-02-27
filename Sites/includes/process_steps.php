<?php
	ob_start();
	
	include 'steps.php';
	
	$action = (isset($_GET['id']) ? 'updated' : 'added');

	try {
		$root = array(
			'_userid'		=>	$_POST['user'],
			'_obsdate'		=>	$_POST['odate'],
			'_stepscount'	=>	$_POST['steps'],
			'_activityid'	=>	$_POST['activity']
		);
		if(isset($_GET['id'])) $root['_id'] = $_GET['id'];
		$object = tblstepscounts::create($root);
		$action = 'record has been ' . $action . '.';
	}
	catch (PDOException $e) {
		$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
	}
	header("Location: display_steps.php?action=" . $action);
	
	ob_flush();
?>
</form>
