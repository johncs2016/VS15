<?php
	//Fetching from your database table.
	
	require_once("weeklydata.php");
	require_once("user.php");
	require_once("details.php");
	require_once("steps.php");
	require_once("exercise.php");
	require_once("medical.php");
	require_once("recipient.php");
	require_once("rectypes.php");
	require_once("conversion.php");
	require_once("composition.php");
	require_once("display_functions.php");
	require_once("blood_pressure.php");
	require_once("body_measurements.php");
	require_once("metadata.php");
	
	try {
		$user_object = tblusers::get($_SESSION['userid']);
		$meta_objects = tblmetadata::getAll();
		$report_dates = array();
		foreach ($meta_objects as $object) {
			$table = $object->getTableName();
			if(!isset($report_dates[$table])) $report_dates[$table] = array();
			$field = $object->getFieldName();
			$report_dates[$table][$field] = array();
			$report_dates[$table][$field]['start'] = $object->getStartDate();
			$report_dates[$table][$field]['end'] = $object->getEndDate();
		}
		$recipient_objects = tblreportrecipients::getAll();
		$bm_object = new bodyMeasurements($user_object->getID());
		$details_object = tbldetails::get($user_object->getDetailsID());
		$fullname = $details_object->fullname();
		$age = $details_object->age();
		$dob = $details_object->getDOB();
		$weight_start_report_date = $report_dates['tblweeklydata']['weight']['start'];
		$waist_start_report_date = $report_dates['tblweeklydata']['waistsize']['start'];
		$weight_end_report_date = $report_dates['tblweeklydata']['weight']['end'];
		$waist_end_report_date = $report_dates['tblweeklydata']['waistsize']['end'];
		$exercise_start_report_date = $report_dates['tblexercise']['activityid']['start'];
		$exercise_end_report_date = $report_dates['tblexercise']['activityid']['end'];
		$cardio_end_date = DateTime::CreateFromFormat("Y-m-d G:i:s",tblcardio::getMinMaxValue('obsdate', $exercise_start_report_date, false, false) . " 00:00:00");
		$strength_end_date = DateTime::CreateFromFormat("Y-m-d G:i:s", tblstrength::getMinMaxValue('obsdate', $exercise_start_report_date, false, false) . " 00:00:00");
		if($cardio_end_date > $exercise_end_report_date) $exercise_end_report_date = $cardio_end_date;
		if($strength_end_date > $exercise_end_report_date) $exercise_end_report_date = $strength_end_date;
		$last_date = DateTime::CreateFromFormat('Y-m-d', tblweeklydata::getMinMaxValue("obsdate", false, false, false));
		$first_date = new DateTime($last_date->format('Y') . '-01-01');
		$current_weight = tblweeklydata::getFirstLastValue("weight", $first_date, false, true);
		$initial_weight = tblweeklydata::getFirstLastValue("weight", $first_date, false, false);
		$first_reported_weight = tblweeklydata::getFirstLastValue("weight", $weight_start_report_date, $weight_end_report_date, false);
		$current_waist_size = tblweeklydata::getFirstLastValue("waistsize", $first_date, false, true);
		$initial_waist_size = tblweeklydata::getFirstLastValue("waistsize", $first_date, false, false);
		$first_reported_waist_size = tblweeklydata::getFirstLastValue("waistsize", $waist_start_report_date, $waist_end_report_date, false);
		$blood_pressure = composition::getReportedBloodPressure(null, 1);
		$last_reported_weight = tblweeklydata::getFirstLastValue("weight", $weight_start_report_date, $weight_end_report_date, true);
		$last_reported_waist_size = tblweeklydata::getFirstLastValue("waistsize", $waist_start_report_date, $waist_end_report_date, true);
		$email_address = $details_object->getEmailAddress();
		$iflag = $user_object->getIsImperial();
		$gflag = $details_object->ismale();
		$height = $bm_object->getHeight();
		$hip = $bm_object->getHipSize();
		$forearm = $bm_object->getForearmSize();
		$wrist = $bm_object->getWristSize();
		$tw = $user_object->getTWIndexid();
		$target_calories = $user_object->getTargetCalories();
		$lower_limit = composition::target_weight($current_weight, $height, $wrist, $current_waist_size, $hip, $forearm, $gflag, $age, $tw, true);
		$upper_limit = composition::target_weight($current_weight, $height, $wrist, $current_waist_size, $hip, $forearm, $gflag, $age, $tw, false);
		$lbm = composition::lean_body_mass($current_weight, $wrist, $current_waist_size, $hip, $forearm, $gflag);
		$bfm = composition::body_fat_mass($current_weight, $lbm);
		$data_objects1_by_date = tblweeklydata::getByDate($weight_start_report_date, $weight_end_report_date);
		$data_objects2_by_date = tblweeklydata::getByDate($waist_start_report_date, $waist_end_report_date);
		$fields = array("Observation Date","Weight","Waist Size");
		$data_const1 = array(
			"canvas"		=>	$data_objects1_by_date[0]->canvas,
			"description"	=>	$data_objects1_by_date[0]->description,
			"units"			=>	$data_objects1_by_date[0]->getUnits()
		);
		$data_const2 = array(
			"canvas"		=>	$data_objects2_by_date[0]->canvas,
			"description"	=>	$data_objects2_by_date[0]->description,
			"units"			=>	$data_objects2_by_date[0]->getUnits()
		);
		$weight_category = array();
		$waist_category = array();
		$weights = array();
		$waists = array();
		foreach ($data_objects1_by_date as $object) {
			$dt_object1 = $object->getObsDate();
			$wt = $object->getWeight();
			if($wt != 0) {
				$weight_category[] = "'" . ($dt_object1->getTimestamp()) . "'";
				$f_weights[] = $wt;
				$weights[] = number_format($wt, 1);
			}
		}
		foreach ($data_objects2_by_date as $object) {
			$dt_object2 = $object->getObsDate();
			$wt = $object->getWaistSize();
			if($wt != 0) {
				$waist_category[] = "'" . ($dt_object2->getTimestamp()) . "'";
				$f_waists = $wt;
				$waists[] = number_format($wt, 1);
			}
		}
		$rec_details = array();
		$rectypes = tblrectypes::getAll();
		$rec_dets[] = array();
		foreach ($rectypes as $type) {
			$key = $type->getType();
			$rec_dets[$key] = array();
		}
		foreach ($recipient_objects as $object) {
			$obj = tbldetails::get($object->getDetailsID());
			$key = tblrectypes::get($object->getRecID())->getType();
			$rec = $obj->getEmailAddress();
			$rec_dets[$key][] = $rec;
			$recipients[] = array(
				'id'		=>	$obj->getID(),
				'type'		=>	$key,
				'details'	=>	$obj->getRecipient(),
				'name'		=>	$obj->fullname(),
				'email'		=>	$rec
			);
		}
		$rec_list = array();
		foreach ($rectypes as $type) {
			$key = $type->getType();
			$rec_list[$key] = implode(', ', $rec_dets[$key]);
		}
		$weight_string = '[' . implode(',',$weights) . ']';
		$waist_string = '[' . implode(',',$waists) . ']';
		$weightcat_string = '[' . implode(',',$weight_category) . ']';
		$waistcat_string = '[' . implode(',',$waist_category) . ']';
	}
	catch (PDOException $e) {
		echo $e->getMessage();
	}
	$db = NULL;
	unset($db);
?>
