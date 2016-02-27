<?php

require_once 'weeklydata.php';

echo getAsJSON();

function getAsJSON() {
    try {
	$db = tblweeklydata::getConnection();
        $sql = 'SELECT obsdate, weight, waistsize FROM tblweeklydata WHERE userid = 1 ORDER BY obsdate';
	$stmt = $db->prepare($sql);
        $stmt->execute();
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $first = $results[0];
        $ret = [];
        $cols = [];
        foreach($first as $key => $value) {
            $arr = [];
            $arr['id'] = $key;
            $arr['label'] = ($key === 'obsdate' ? 'Date' : ($key === 'weight' ? 'Weight' : 'Waist Size'));
            $arr['type'] = ($key === 'obsdate' ? 'datetime' : 'number');
            array_push($cols, $arr);
        }
        $ret['cols'] = $cols;
        $fullArr = [];
        foreach($results as $row) {
            $arr2 = [];
            $rowArr = [];
            foreach($row as $key => $value) {
                $arr = [];
                if ($key === 'obsdate') {
                    $dte = new DateTime($value);
                    $d = (int)($dte->format('d'));
                    $m = (int)($dte->format('m')) - 1;
                    $y = (int)($dte->format('Y'));
                    $arr['v'] = 'new Date(' . (string)$y . ', ' . (string)$m . ', ' . (string)$d . ', 0, 0, 0)';
                } else {
                    $arr['v'] = number_format((float)$value, 1);
                }
                array_push($rowArr, $arr);
            }
            $arr2['c'] = $rowArr;
            array_push($fullArr, $arr2);
        }
        $ret['rows'] = $fullArr;
	return json_encode($ret);
    } catch (PDOException $e) {
	echo $e->getMessage();
	return false;
    }
 }
		
