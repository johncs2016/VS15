<?php
	ob_start();
	include_once "sessions.php";
	require_once("prepare_data.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Health Records Application</title>
</head>
<body>
	<header id="phead">Health Records Summary Report For <?php echo $fullname; ?></header>
	<section id="idata">
		<div id="dmain">
			<div id="pdpersonal" class="hdata">
				<table>
					<thead>
						<tr class="dhead">
							<th class="chead" colspan="4">Personal Details</th>
						</tr>
					</thead>
					<tbody>
						<tr class="drow">
							<td class="tlabel">Full Name </td>
							<td class="tdata bpersonal aleft"><?php echo $fullname; ?></td>
							<td class="tlabel">Date Of Birth</td>
							<td class="tdata bpersonal aleft"><?php echo display::display_date($dob); ?></td>
						</tr>
						<tr class="drow">
							<td class="tlabel">Age </td>
							<td class="tdata bpersonal aleft"><?php echo $age; ?></td>
							<td class="tlabel">Frame Size</td>
							<td class="tdata bpersonal aleft"><?php echo display::str_frame_size($height, $wrist); ?></td>
						</tr>
						<tr class="drow">
							<td class="tlabel">Height</td>
							<td class="tdata bpersonal aleft"><?php echo display::display_value($height, 3, $iflag); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="dinitial" class="hdata">
				<table>
					<thead>
						<tr class="dhead">
							<th class="chead" colspan="4">Initial Conditions</th>
						</tr>
					</thead>
					<tbody>
						<tr class="drow">
							<td class="tlabel">Initial Weight</td>
							<td class="tdata bweight aright"><?php echo display::display_value($initial_weight, 1, $iflag); ?></td>
							<td class="tlabel">Lower Weight Limit</td>
							<td class="tdata bweight aright"><?php echo display::display_value($lower_limit, 1, $iflag); ?></td>
						</tr>
						<tr class="drow">
							<td class="tlabel">Initial Waist Size</td>
							<td class="tdata bwaist aright"><?php echo display::display_value($initial_waist_size, 2, $iflag); ?></td>
							<td class="tlabel">Upper Weight Limit</td>
							<td class="tdata bweight aright"><?php echo display::display_value($upper_limit, 1, $iflag); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div id="dlatest" class="hdata">
			<table>
				<thead>
					<tr class="dhead">
						<th class="chead" colspan="4">Summary Of Latest Data</th>
					</tr>
				</thead>
				<tbody>
					<tr class="drow">
						<td class="tlabel">Current Weight</td>
						<td class="tdata bweight bweight aright"><?php echo display::display_value($current_weight, 1, $iflag); ?></td>
						<td class="tlabel">Normal Gym Sessions In Last Month</td>
						<td class="tdata bactivity amiddle"><?php echo display::exercise_count($exercise_start_report_date, $exercise_end_report_date); ?></td>
					</tr>
					<tr class="drow">
						<td class="tlabel">Weekly Weight Change</td>
						<td class="tdata bweight aright"><?php echo display::display_change(display::getDifference("weight", $weight_start_report_date, $weight_end_report_date), false, $iflag); ?></td>
						<td class="tlabel">RPM Sessions In Last Month</td>
						<td class="tdata bactivity amiddle"><?php echo display::activities_count(2, $exercise_start_report_date, $exercise_end_report_date); ?></td>
					</tr>
					<tr class="drow">
						<td class="tlabel">Total Weight Change</td>
						<td class="tdata bweight aright"><?php echo display::display_change(display::getDifference("weight", $first_date, false), false, $iflag); ?></td>
						<td class="tlabel">Bodystep Sessions In Last Month</td>
						<td class="tdata bactivity amiddle"><?php echo display::activities_count(4, $exercise_start_report_date, $exercise_end_report_date); ?></td>
					</tr>
					<tr class="drow">
						<td class="tlabel">Lowest Weight So Far</td>
						<td class="tdata bweight aright"><?php echo display::display_value(tblweeklydata::getMinMaxValue("weight", $first_date, false, true), 1, $iflag); ?></td>
						<td class="tlabel">Aquafit Sessions In last Month</td>
						<td class="tdata bactivity amiddle"><?php echo display::activities_count(5, $exercise_start_report_date, $exercise_end_report_date); ?></td>
					</tr>
					<tr class="drow">
						<td class="tlabel">Highest Weight So Far</td>
						<td class="tdata bweight aright"><?php echo display::display_value(tblweeklydata::getMinMaxValue("weight", $first_date, false, false), 1, $iflag); ?></td>
						<td class="tlabel">Current Waist Size</td>
						<td class="tdata bwaist aright"><?php echo display::display_value($current_waist_size, 2, $iflag); ?></td>
					</tr>
					<tr class="drow">
						<td class="tlabel">Current Blood Pressure</td>
						<td class="tdata bother aright"><?php echo $blood_pressure->display(); ?></td>
						<td class="tlabel">Change In Waist Size This Month</td>
						<td class="tdata bwaist aright"><?php echo display::display_change(display::getDifference("waistsize", $waist_start_report_date, $waist_end_report_date), true, $iflag); ?></td>
					</tr>
					<tr class="drow">
						<td class="tlabel"></td>
						<td class="tlabel"></td>
						<td class="tlabel">Total Change in Waist Size</td>
						<td class="tdata bwaist aright"><?php echo display::display_change(display::getDifference("waistsize", $first_date, false), true, $iflag); ?></td>
					</tr>
					<tr class="drow">
					</tr>
				</tbody>
			</table>
		</div>
	</section>
	<section id="pcharts">
		<?php	for($i = 1; $i <= 2; $i++) { ?>
		<div class="phchart">
			<img id="p<?php echo $data_const["canvas"][$i]; ?>" src="../images/p<?php echo ($i == 1 ? $data_const1["canvas"][$i] : $data_const2["canvas"][$i]); ?>.jpg" alt="<?php echo (($i == 1) ? 'Weight' : 'Waist Size'); ?>Chart">
		</div>
		<?php } ?>
	</section>
</body>
</html>
<?php
	$pdfhtml = ob_get_contents();
	$stylesheet = file_get_contents("../css/report.css");
	ob_end_clean();
	require_once("mpdf/mpdf.php");
	$mpdf = new mPDF('utf-8', 'A4-L', 0, '', 0, 0, 0, 0, 0, 0);
	$mpdf->allow_output_buffering = true;
	$mpdf->PDFA = true;
	$mpdf->WriteHTML($stylesheet, 1);
	$mpdf->WriteHTML($pdfhtml);
	$mpdf->Output();
	exit;
?>