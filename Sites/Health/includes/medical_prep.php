<?php
	ob_start();

	require_once("user.php");
	require_once("weeklydata.php");
	require_once("validator.php");

	$medid = (isset($_GET['id']) ? $_GET['id'] : -1);
	$dataid = $_GET['dataid'];
	$userid = $_GET['userid'];

	$addmed = ($medid < 1);
	$addrec = ($dataid < 1);

	try {

		$properties = array(
			'_userid'	=>	$_GET['userid'],
			'_obsdate'	=>	$_GET['obsdate'],
			'_weight'	=>	$_GET['weight'],
			'_waistsize'	=>	$_GET['waist']
		);

		if (empty($properties['_weight'])) {
			$properties['_weight'] = 0;
		}

		if (empty($properties['_waistsize'])) {
			$properties['_waistsize'] = 0;
		}

		$val_object = new validator();
		$valid = $val_object->validate_date("obsdate", $properties["_obsdate"]);
		$valid = ($valid && $val_object->validate_float("weight", $properties["_weight"]));
		$valid = ($valid && $val_object->validate_float("waistsize", $properties["_waistsize"]));
		$error_array = $val_object->getAllErrors();

		$user = tblusers::get($userid);
	
		if ($user == null) {
			if (isset($error_array['user'])) $error_array['user'] .= "\n"; else $error_array['user'] = '';
			$error_array['user'] .= "User Does Not Exist";
			$valid = false;
		}
			
		if (empty($error_array)) {
			if($addrec) {
				$object = tblweeklydata::create($properties);
			} else {
				$object = tblweeklydata::get($dataid);
				$object->update($properties);
			}
			$url = "medical_form.php?" . (!$addmed ? "id=$medid" : "dataid=$dataid");
		} else {
			$action = "Error: Invalid Weekly Data entered, cannot " . ($addmed ? "add new" : "edit") . " blood pressure data.";
			$url = "dataForm.php?" . (!$addrec ? "id={$dataid}&" : "") ."action=$action";
		}
		header("Location: $url");
		exit;
	} catch (PDOException $e) {
		$action = 'Error: Record cannot be ' . ($addrec ? "created" : "updated") . '.<br>' . $e->getMessage();
		$url = "dataForm.php?" . (!$addrec ? "id={$dataid}&" : "") ."action=$action";
		header("Location: $url");
		exit();
	}

	ob_end_flush();
?>
