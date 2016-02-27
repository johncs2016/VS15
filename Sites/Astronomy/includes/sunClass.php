<?php
require_once('includes/dateClass.php');
$latitude = ini_get("date.default_latitude");
$longitude = ini_get("date.default_longitude");
list($y, $m) = array(2016, 1);

function hms($val) {
    // convert seconds to hours:minutes:seconds
    $v=$val;
    $hrs=intval($v/3600);
    $v-=($hrs*3600); // subtract hours
    $mins=intval($v/60); 
    $v-=($mins*60); // subtract minutes
    $secs=$v % 60; // seconds remaining
    if ($mins<10) {$mins="0".$mins;}
    if ($secs<10) {$secs="0".$secs;}
    return $hrs.":".$mins.":".$secs;
}

function dateFromTimeStamp($time) {
    $objDate = new dateClass();
    $objDate->setTimestamp($time);
    return $objDate;
}

function getHorizon($type, $set = false) {
    if (!array_key_exists($type, type_array($set))) {
        return false;
    } else {
        return 90 + 6 * $type + ((5 / 6) * ($type == 0));
    }
}

function beg_end($riseset = true, $set = false) {
    //return (($riseset == true ? 1 : 0) . ' ' . ($set == true ? 1 : 0));
    return ($riseset == true ? ($set == true ? "set" : "rise") : ($set == true ? "End" : "Start"));
}

function type_array($set = false) {
    $arr = array(
        0   =>  "Time Of Sun" . beg_end(true, $set),
        1   =>  "Civil Twilight " . beg_end(false, $set) . "s",
        2   =>  "Nautical Twilight " . beg_end(false, $set) . "s",
        3   =>  "Astronomical Twilight " . beg_end(false, $set) . "s"
    );
    return $arr;
}

function type_record($type = 0, $set = false) {
    $arr = type_array($set);
    //return $set;
    return $arr[$type];
}

function fix_range($val, $min, $max, $step) {
    while (($val < $min) || ($val > $max)) {
        if ($val < $min) {
            $val += abs($step);
        } elseif ($val > $max) {
            $val -= abs($step);
        }
    }
    return $val;
}

function rad2degs($arg) {
    return $arg * 180 / pi();
}

function deg2rads($arg) {
    return $arg * pi() / 180;
}

function suninfo($d, $m, $y, $lat, $long, $type = 0, $set = false) {
    $dteSun = new dateClass($y . "-" . ($m < 10 ? '0' : '') . $m . '-' . ($d < 10 ? '0' : ''). $d . " 00:00:00", "UTC");
    $ts = $dteSun->getTimestamp();
    $zenith = getHorizon($type, $set);
    $N = $dteSun->getDayNumber();
    $lngHour = $long / 15;
    $t = $N + (((6 + ($set == true ? 12 : 0)) - $lngHour) / 24);
    $M = (0.9856 * $t) - 3.289;
    $L = fix_range($M + (1.916 * sin(deg2rads($M))) + (0.020 * sin(deg2rads(2 * $M))) + 282.634, 0, 360, 360);
    $RA = fix_range(rad2degs(atan(0.91764 * tan(deg2rads($L)))), 0, 360, 360);
    $Lquadrant  = (floor($L / 90)) * 90;
    $RAquadrant = (floor($RA / 90)) * 90;
    $RA += ($Lquadrant - $RAquadrant);
    $RA /= 15;
    $sinDec = 0.39782 * sin(deg2rads($L));
    $cosDec = cos(asin($sinDec));
    $cosH = (cos(deg2rads($zenith)) - $sinDec * sin(deg2rads($lat))) / ($cosDec * cos(deg2rads($lat)));
    if ($cosH > 1) {
        return "Always Below";
    } elseif ($cosH < -1) {
        return "Always Above";
    } else {
        $H = ((360 * ($set == true ? 0 : 1)) + ($set == true ? 1 : -1 ) * rad2degs(acos($cosH))) / 15;
        $T = $H + $RA - (0.06571 * $t) - 6.622;
        $UT = 3600 * fix_range($T - $lngHour, 0, 24, 24);
        $dteSun->setTimestamp($ts + $UT);
        $dteSun->setToLocalTime();
        $dteSun->setShowSeconds(true);
        return $dteSun;
    }
}

function get_sunrise_set($d, $m, $y, $lat, $long, $set = false) {
    return suninfo($d, $m, $y, $lat, $long, 0, $set);
} 

function get_sunrise($d, $m, $y, $lat, $long) {
    return get_sunrise_set($d, $m, $y, $lat, $long, false);
}

function get_sunset($d, $m, $y, $lat, $long) {
    return get_sunrise_set($d, $m, $y, $lat, $long, true);
}

function get_Civil($d, $m, $y, $lat, $long, $set = false) {
    return suninfo($d, $m, $y, $lat, $long, 1, $set);
}

function get_Civil_Begin($d, $m, $y, $lat, $long) {
    return get_Civil($d, $m, $y, $lat, $long, false);
}

function get_Civil_End($d, $m, $y, $lat, $long) {
    return get_Civil($d, $m, $y, $lat, $long, true);
}

function get_Nautical($d, $m, $y, $lat, $long, $set = false) {
    return suninfo($d, $m, $y, $lat, $long, 2, $set);
}

function get_Nautical_Begin($d, $m, $y, $lat, $long) {
    return get_Nautical($d, $m, $y, $lat, $long, false);
}

function get_Nautical_End($d, $m, $y, $lat, $long) {
    return get_Nautical($d, $m, $y, $lat, $long, true);
}

function get_Astronomical($d, $m, $y, $lat, $long, $set = false) {
    return suninfo($d, $m, $y, $lat, $long, 3, $set);
}

function get_Astronomical_Begin($d, $m, $y, $lat, $long) {
    return get_Astronomical($d, $m, $y, $lat, $long, false);
}

function get_Astronomical_End($d, $m, $y, $lat, $long) {
    return get_Astronomical($d, $m, $y, $lat, $long, true);
}

function daylightLength($d, $m, $y, $lat, $long) {
    $dte_sunrise = get_sunrise($d, $m, $y, $lat, $long);
    $dte_sunset = get_sunset($d, $m, $y, $lat, $long);
    if ($dte_sunrise == "Always Above") {
        return hms(86400);
    } elseif ($dte_sunset == "Always Below") {
        return hms(0);
    } else {
        return hms($dte_sunset->getTimestamp() - $dte_sunrise->getTimestamp());
    }
}
