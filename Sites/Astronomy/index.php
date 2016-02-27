<!DOCTYPE html>
<html>
    <head>
	<meta charset="utf-8">
	<title>Sunrise/set Application</title>
        <?php require_once("includes/sunClass.php"); ?>
        <link rel="stylesheet" type="text/css" href="css/list.css">
    </head>
    <body>
        <div id="lhead">
            <h1 id="ltitle">Sunrise and Sunset Times For Edinburgh</h1>
        </div>
        <div id="data">
        <table>
            <tr class="dhead">
                <th class="hodd">Observation Date</th>
                <th class="heven"><?php echo type_record(3, false); ?></th>
                <th class="hodd"><?php echo type_record(2, false); ?></th>
                <th class="heven"><?php echo type_record(1, false); ?></th>
                <th class="hodd"><?php echo type_record(0, false); ?></th>
                <th class="heven"><?php echo type_record(0, true); ?></th>
                <th class="hodd"><?php echo type_record(1, true); ?></th>
                <th class="heven"><?php echo type_record(2, true); ?></th>
                <th class="hodd"><?php echo type_record(3, true); ?></th>
                <th class="heven">DayLight Length</th>
            </tr>
<?php
    for($d = 1; $d <= cal_days_in_month(CAL_GREGORIAN, $m, $y); $d++) { ?>
            <tr class="drow">
<?php
    $dte = new dateClass(implode('-', array($y, ($m < 10 ? '0' : '') . $m, ($d < 10 ? '0' : '') . $d)) . " 00:00:00", "UTC");
    $dte->setToLocalTime();
?>
                <td class="dodd"><?php echo $dte->getDate(); ?></td>
<?php
    $sun_array = array(
        get_Astronomical_Begin($d, $m, $y, $latitude, $longitude),
        get_Nautical_Begin($d, $m, $y, $latitude, $longitude),
        get_Civil_Begin($d, $m, $y, $latitude, $longitude),
        get_sunrise($d, $m, $y, $latitude, $longitude),
        get_sunset($d, $m, $y, $latitude, $longitude),
        get_Civil_End($d, $m, $y, $latitude, $longitude),
        get_Nautical_End($d, $m, $y, $latitude, $longitude),
        get_Astronomical_End($d, $m, $y, $latitude, $longitude),
        daylightLength($d, $m, $y, $latitude, $longitude)
    );
    $i = 1;
    foreach ($sun_array as $value) {
        $i++;
?>
                <td class="d<?php echo ($i % 2 == 0 ? "even" : "odd"); ?>"><?php echo (gettype($value) == "string" ? $value : $value->getTime()); ?></td>
<?php } } ?>
            </tr>
        </table>
        </div>
    </body>
</html>
