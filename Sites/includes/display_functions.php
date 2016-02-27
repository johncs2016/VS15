<?php
	require_once("conversion.php");
	require_once("measurements.php");
	require_once("cardio.php");
	require_once("strength.php");

	class display {
		
		private function __construct() {
			
		}
		
		private function __clone() {
			
		}

		public static function display_value($value, $v = 1, $iflag = false) {
			$obj = tbltypesofmeasurements::get($v);
			$class = $obj->getType();
			$object = new $class($value, $v, $iflag);
			return $object->display();
		}

		public static function display_change($value, $vflag = false, $iflag = false) {
			
			$v = ($vflag ? 2 : 1);
			$zero = self::display_value(0, $v, $iflag);
			$str_value = self::display_value(abs($value), $v, $iflag);

			return ($str_value == $zero ? "No Change" : ($value < 0 ? "Lost" : "Gained") . " " . $str_value);
		}

		public static function display_date($dte, $format = null) {
			if(!isset($format)) $format = "d/m/y";
			return $dte->format($format);
		}

		public static function affordableLossGain($value, $lower, $upper, $flag = false) {

			$return = ($flag ? $upper - $value : $value - $lower);
			if($return < 0) {
				$return = 0;
			}

			return $return;
		}

		public static function str_frame_size($height, $wrist) {
			$fsr = 100 * $height / $wrist;
			return ($fsr > 10.4) ? "Small" : (($fsr < 9.6) ? "Large" : "Medium") . " Frame";
		}

		public static function getLatestDifference($field, $id = false) {
		
			$id = ($id ? $id : tblweeklydata::getFirstLastValue('id', false, false, true));
			$object = tblweeklydata::get($id);
			$odate = $object->getObsDate();
			$prev = tblweeklydata::getPrevNextValue($field, $odate, true);
			$curr = tblweeklydata::getFirstlastValue($field, false, $odate, true);
			
			return ($curr - $prev);
		}
		
		public static function getDifference($field, $startdate = false, $enddate = false) {

			$startDate = tblweeklydata::fixDate($startdate, false);
			$endDate = tblWeeklyData::fixDate($enddate, true);

			$prev = tblweeklydata::getFirstLastValue($field, $startdate, $enddate, false);
			$next = tblweeklydata::getFirstLastValue($field, $startdate, $enddate, true);

			return ($next - $prev);
		}
		
		public static function activities_count($id, $startdate = false, $enddate = false) {
			
			$startdate = tblexercise::fixDate($startdate, true);
			$enddate = tblexercise::fixDate($enddate, false);

			$str_startdate = $startdate->format('Y-m-d');
			$str_enddate = $enddate->format('Y-m-d');
			
			$db = tblexercise::getConnection();

			$select = "SELECT COUNT(*) AS activities FROM tblexercise WHERE ((activityid = :id) AND (obsdate BETWEEN :startdate AND :enddate))";
			
			$statement = $db->prepare($select);

			$statement->bindparam(':id', $id, PDO::PARAM_INT);
			$statement->bindparam(':startdate', $str_startdate, PDO::PARAM_STR);
			$statement->bindparam(':enddate', $str_enddate, PDO::PARAM_STR);
			$statement->execute();
			
			$data = $statement->fetch(PDO::FETCH_ASSOC);

			$db = null;

			return $data['activities'];
		}
		
		public static function exercise_count($startdate = false, $enddate = false) {

			$startdate = tblcardio::fixDate($startdate, true);
			$enddate = tblcardio::fixDate($enddate, false);

			$str_startdate = $startdate->format('Y-m-d');
			$str_enddate = $enddate->format('Y-m-d');
			
			$db = tblcardio::getConnection();
			
			$sel_fields = "SELECT id, userid, obsdate, venueid, equipid FROM tbl";
			$wh1 = " WHERE obsdate BETWEEN :startdate1 AND :enddate1";
			$wh2 = " WHERE obsdate BETWEEN :startdate2 AND :enddate2";
			$s1 = $sel_fields . 'cardio' . $wh1;
			$s2 = $sel_fields . 'strength' . $wh2;

			$select = $s1 . ' UNION ' . $s2;
			$select = 'SELECT * FROM (' . $select . ') AS c GROUP BY obsdate';
			$select = 'SELECT COUNT(*) AS ex_count FROM (' . $select . ') AS d';
			
			$statement = $db->prepare($select);

			$statement->bindvalue(':startdate1', $str_startdate, PDO::PARAM_STR);
			$statement->bindvalue(':enddate1', $str_enddate, PDO::PARAM_STR);
			$statement->bindvalue(':startdate2', $str_startdate, PDO::PARAM_STR);
			$statement->bindvalue(':enddate2', $str_enddate, PDO::PARAM_STR);
			$statement->execute();
			
			$data = $statement->fetch(PDO::FETCH_ASSOC);

			$db = null;

			return $data['ex_count'];
		}
		
		private static function week_num($flag = true, $wb = true) {
			return ($flag == true ? 1 : 7) + self::wb_num($wb);
		}
		
		private static function wb_num($wb = true) {
			return ($wb == true ? 0 : 1);
		}

		public static function week_sql($flag = true, $wb = true) {
			return "DATE_ADD(obsdate, INTERVAL " . self::week_num($flag, $wb) . " - DAYOFWEEK(obsdate) DAY) AS week_" . ($flag == true ? "commenc" : "end") . "ing";
		}
		
		private static function str_yearweek($wb = true) {
			return "YEARWEEK(obsdate, " . self::wb_num($wb) . ")";
		}
		
		public static function group_sql($wb = true) {
			$yw = self::str_yearweek($wb);
			return "GROUP BY LEFT($yw, 4), RIGHT($yw, 2)";
		}
	}
?>