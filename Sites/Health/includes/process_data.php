<?php
	ob_start();
	
	include 'weeklydata.php';
	
	$error_array = array();

	$action = (isset($_GET['id']) ? 'updated' : 'added');

	try {
		$properties = array(
			'_userid'		=>	check_input('userid',$_POST['user'],'A user must be selected'),
			'_obsdate'		=>	check_input('obsdate',$_POST['odate'],'A date must be selected'),
			'_weight'		=>	check_input('weight',$_POST['weight']),
			'_waistsize'	=>	check_input('waistsize',$_POST['waist'])
		);
		if(isset($_GET['id'])) $properties['_id'] = $_GET['id'];
		$object = tblweeklydata::create($properties);
		$action = 'record has been ' . $action . '.';
	}
	catch (PDOException $e) {
		$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
	}
	header("Location: display_data.php?action=" . $action);
	
	ob_flush();
	
	function check_input($key, $data, $error='') {

		global $error_array;

		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		if ($error && (strlen($data) == 0)) {
			$error_array[$key] = $error;
		}
		return $data;
	}
?>