<?php
	require_once("conversion.php");
	require_once("framesize.php");
	require_once("medical.php");
	require_once("blood_pressure.php");

	class composition {
		
		private static $frameObject;
		
		private function __construct() {
			
		}
		
		private function __clone() {
			
		}
		
		public static function distance_walked($stepsize, $stepscount) {
			return $stepsize * $stepscount / 1000;
		}
		
		public static function waist2height($waist, $height) {
			return $waist / $height;
		}
		
		public static function body_mass_index($weight, $height) {
			return $weight / ($height * $height);
		}
		
		public static function bmi_category($bmi, $gflag) {
			$catarray = array(
				1 => "Very Severely Underweight",
				2 => "Severely Underweight",
				3 => "Underweight",
				4 => "Normal Weight",
				5 => "Overweight",
				6 => "Obese",
				7 => "Severely Obese",
				8 => "Morbidly Obese",
				9 => "Super Obese"
			);
			$bmiarray = array(
				1 => 15,
				2 => 16,
				3 => 18.5,
				4 => 25,
				5 => 30,
				6 => 35,
				7 => 40,
				8 => 50
			);
			$idx = 0;
			$test = 0;
			if ($bmi > $bmiarray[8]) {
				return $catarray[9];
			}
			else {
				while (($bmi > $test) && ($idx <= 8)) {
					$idx++;
					$test = $bmiarray[$idx];
				}
				return $catarray[$idx];
			}
		}
		
		public static function BMI_Target_Weight($height, $gflag, $flag) {
			$factor = ($flag ? 18.5 : 25.0); 
			return $factor * $height * $height;
		}
		
		public static function lean_body_mass($weight, $wrist, $waist, $hip, $forearm, $gflag) {
			$factor = array(
				1 => (conversions::totPOUNDfromKG($weight) * ($gflag ? 1.082 : 0.732)) + ($gflag ? 94.42 : 8.987),
				2 => $gflag ? 0 : (conversions::INfromCM($wrist) / 3.14),
				3 => ($gflag ? 4.15 : 0.157) * conversions::INfromCM($waist),
				4 => $gflag ? 0 : (0.249 * conversions::INfromCM($hip)),
				5 => $gflag ? 0 : (0.434 * conversions::INfromCM($forearm))
			);
			return conversions::totPOUNDStoKG($factor[1] + $factor[2] - $factor[3] - $factor[4] + $factor[5]);
		}
		
		public static function body_fat_mass($weight, $lbm) {
			return $weight - $lbm;
		}
		
		public static function fat_percentage($weight, $bfm) {
			return $bfm / $weight;
		}
		
		public static function body_fat_limits($age, $gflag, $i) {
			switch ($age) {
				case (($age >= 20) && ($age < 40)):
					$limits = array(
						1 => $gflag ? 8 : 21,
						2 => $gflag ? 20 : 33,
						3 => $gflag ? 25 : 39
					);
					break;
				
				case (($age >= 40) && ($age < 60)):
					$limits = array(
						1 => $gflag ? 11 : 23,
						2 => $gflag ? 22 : 34,
						3 => $gflag ? 28 : 40
					);
					break;
				
				case ($age >= 60):
					$limits = array(
						1 => $gflag ? 13 : 24,
						2 => $gflag ? 25 : 36,
						3 => $gflag ? 30 : 42
					);
					break;
				
				default:
					$limits = array(
						1 => 0,
						2 => 0,
						3 => 0
					);
					
					break;
			}
			return (($i >= 1) && ($i <= 3)) ? $limits[$i] : 0;
		}
		
		public static function body_fat_category($fatpercent, $age, $gflag) {
			$p = 100 * $fatpercent;
			if ($age < 20) {
				return "Too Young";
			}
			elseif ($p < self::body_fat_limits($age, $gflag, 1)) {
				return "Underfat";
			}
			elseif ($p < self::body_fat_limits($age, $gflag, 2)) {
				return "Healthy";
			}
			elseif ($p < self::body_fat_limits($age, $gflag, 3)) {
				return "Overfat";
			}
			else {
				return "Obese";
			}
		}
		
		public static function bf_target_weight($lbm, $age, $gflag, $flag) {
			return $lbm / (1 - (self::body_fat_limits($age, $gflag, !$flag + 1) /100));
		}
		
		private static function get_frame_sizes() {
			$frameXML = simplexml_load_file("../XML/FrameSizes.xml");
			self::$frameObject = array();
			foreach ($frameXML->FrameSize as $key => $SizeObject) {
				$fo = new framesize;
				$fo->setID((string)$SizeObject->ID);
				$fo->setGender((string)$SizeObject->Gender);
				$fo->setStrHeight((string)$SizeObject->Height);
				$fo->setHeight();
				$fo->setStrWeight('Small', (string)$SizeObject->Small);
				$fo->setStrWeight('Medium', (string)$SizeObject->Medium);
				$fo->setStrWeight('Large', (string)$SizeObject->Large);
				$fo->setWeights();
				self::$frameObject[] = $fo;
			}
		}
		
		public static function fs_target_weight($height, $wrist, $gflag, $flag) {
			$found = False;
			self::get_frame_sizes();
			$i = -1;
			while (!$found && ($i < count(self::$frameObject))) {
				$i++;
				$frame = self::$frameObject[$i];
				$found = (($gflag == ($frame->getGender() == "Male")) && round($height, 2) == round($frame->getHeight(), 2));
			}
			$key = display::str_frame_size($height, $wrist);
			return conversions::totPOUNDStoKG($flag ? self::$frameObject[$i]->getMinMaxWeight($key, true) : self::$frameObject[$i]->getMinMaxWeight($key, false));
		}
		
		public static function target_weight($weight, $height, $wrist, $waist, $hip, $forearm, $gflag, $age, $tw, $flag) {
			switch ($tw) {
				case 0:
					$lbm = self::lean_body_mass($weight, $wrist, $waist, $hip, $forearm, $gflag);
					return self::bf_target_weight($lbm, $age, $gflag, $flag);
					break;
				
				case 1:
					return self::fs_target_weight($height, $wrist, $gflag, $flag);
					break;
					
				case 2:
					return self::BMI_Target_Weight($height, $gflag, $flag);
					break;

				default:
					return False;
					break;
			}
		}
		
		public static function excess_body_mass($weight, $wrist, $waist, $hip, $forearm, $gflag, $age, $tw) {
			$target = array(
				1 => self::target_weight($weight, $wrist, $waist, $hip, $forearm, $gflag, $age, $tw, True),
				2 => self::target_weight($weight, $wrist, $waist, $hip, $forearm, $gflag, $age, $tw, False)
			);
			return ($weight > $target[2]) ? ($weight - $target[2]) : (($weight < $target[1]) ? ($weight - $target[1]) : 0);
		}
		
		public static function basal_metabolic_rate($lbm) {
			return 370 + (21.6 * $lbm);
		}
		
		public static function resting_metabolic_rate($lbm) {
			return 500 + (22 * $lbm);
		}

		public static function getLatestBloodPressures($startdate = null, $enddate = null) {
		
			if($startdate == null) $startdate = false;
			if($enddate == null) $enddate = false;
			$startdate = tblmedical::fixDate($startdate, true);
			$enddate = tblmedical::fixDate($enddate, false);
			
			$rows = tblmedical::getByDate($startdate, $enddate);

			$return = array();
			foreach ($rows as $row) {
				$return[] = $row->bp_object;
			}

			return (count($return) > 0 ? $return : false);
		}

		public static function getReportedBloodPressure($odate = null, $idx = 0) {

			if($odate == null) $odate = DateTime::CreateFromFormat('Y-m-d H:i:s', tblmedical::getMinMaxValue('obsdate', false, false, false) . ' 00:00:00');
			
			$objects = self::getLatestBloodPressures($odate);
			$sumBPs = array();

			if(!$objects) {
				return false;
			}

			$num = count($objects);
			foreach($objects as $object) {
				$systolic = $object->getSystolic();
				$diastolic = $object->getDiastolic();
				$minsys = (isset($minsys) ? ($systolic < $minsys ? $systolic : $minsys) : $systolic);
				$mindia = (isset($mindia) ? ($diastolic < $mindia ? $diastolic : $mindia) : $diastolic);
				$maxsys = (isset($maxsys) ? ($systolic > $maxsys ? $systolic : $maxsys) : $systolic);
				$maxdia = (isset($maxdia) ? ($diastolic > $maxdia ? $diastolic : $maxdia) : $diastolic);
				$totsys = $systolic + (isset($totsys) ? $totsys : 0);
				$totdia = $diastolic + (isset($totdia) ? $totdia : 0);
			}

			$avgsys = $totsys / $num;
			$avgdia = $totdia / $num;

			$return = new bloodPressure(($idx == 0 ? $avgsys : ($idx == 1 ? $minsys : $maxsys)), ($idx == 0 ? $avgdia : ($idx == 1 ? $mindia : $maxdia)));

			return $return;
		}
	}
?>
