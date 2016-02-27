<?php
	include_once("includes/sessions.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Health Records Application</title>
		<?php
			require_once("includes/prepare_data.php");
			require_once("includes/load_libraries.php");
			loadJavascript("loader.js");
		?>
		<link rel="shortcut icon" href="images/favicon.ico" />
		<script>
			loadCSS("dashboard.css");
			require("graphs.js");

			window.onload = function (e)
			{
				var weight_cats = <?php echo $weightcat_string; ?>;
				var waist_cats = <?php echo $waistcat_string; ?>;
				loadGraph(weight_cats, <?php echo $weight_string; ?>, true, true);
				loadGraph(waist_cats, <?php echo $waist_string; ?>, false, true);
			}

			function dump(obj) {
				var out = '';
				for (var i in obj) {
					out += i + ": " + obj[i] + "\n";
				}

				var pre = document.createElement('pre');
				pre.innerHTML = out;
				document.body.appendChild(pre)
			}

		</script>
	</head>
	<body>
		<header id="shead">Health Records Application</header>
		<section id="itop">
			<div id="sdpersonal" class="hdata">
				<div class="dhead">
					<header class="thead">Personal Details</header>
				</div>
				<div class="drow">
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Full Name</p>
						<p class="tvalue aleft"><?php echo $fullname ?></p>
					</div>
					<div class="dcon twocols">
						<p class="tlabel ">&nbsp; Date Of Birth</p>
						<p class="tvalue aleft"><?php echo display::display_date($dob) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Age</p>
						<p class="tvalue aleft"><?php echo $age ?></p>
					</div>
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Frame Size</p>
						<p class="tvalue aleft"><?php echo display::str_frame_size($height, $wrist) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Height</p>
						<p class="tvalue aleft"><?php echo display::display_value($height, 3, $iflag) ?></p>
					</div>
				</div>
			</div>
			<div id="dwaist" class="hdata">
				<header class="thead">Annual Waist Size Summary</header>
				<div class="drow">
					<div class="dcon onecol">
						<p class="tlabel">&nbsp; Initial Waist Size</p>
						<p class="tvalue aright"><?php echo display::display_value($initial_waist_size, 2, $iflag) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon onecol">
						<p class="tlabel">&nbsp; Current Waist Size</p>
						<p class="tvalue aright"><?php echo display::display_value($current_waist_size, 2, $iflag) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon onecol">
						<p class="tlabel">&nbsp; Monthly Waist Size Change</p>
						<p class="tvalue aright"><?php echo display::display_change(display::getDifference("waistsize", $waist_start_report_date, $waist_end_report_date), true, $iflag) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon onecol">
						<p class="tlabel">&nbsp; Total Change In Waist Size So Far</p>
						<p class="tvalue aright"><?php echo display::display_change(display::getDifference("waistsize", $first_date, false), true, $iflag) ?></p>
					</div>
				</div>
			</div>
			<div id="dactivity" class="hdata">
				<header class="thead">Monthly Activities Summary</header>
				<?php
					require_once("includes/activities.php");
					$activities = tblactivities::getAll();
                        	?>
				<div class="drow">
					<div class="dcon onecol">
						<p class="tlabel">&nbsp; Number Of Normal Gym Sessions</p>
						<p class="tvalue amiddle"><?php echo display::exercise_count($exercise_start_report_date, $exercise_end_report_date) ?></p>
					</div>
				</div>
				<?php
					foreach ($activities as $activity) {
						if($activity->getShowActivity() == true) {
				?>
				<div class="drow">
					<div class="dcon onecol">
						<p class="tlabel">&nbsp; Number Of <?php echo $activity->getActivity(); ?>s</p>
						<p class="tvalue amiddle"><?php echo display::activities_count($activity->getID(), $exercise_start_report_date, $exercise_end_report_date) ?></p>
					</div>
				</div>
				<?php
						} // ENDIF
					} // END OF FOREACH STATEMENT
				?>
			</div>
		</section>
		<section id="ibottom">
			<div id="dweight" class="hdata">
				<header class="thead">Annual Weight Summary</header>
				<div class="drow">
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Initial Weight</p>
						<p class="tvalue aright"><?php echo display::display_value($initial_weight, 1, $iflag) ?></p>
					</div>
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Current Weight</p>
						<p class="tvalue aright"><?php echo display::display_value($current_weight, 1, $iflag) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Lower Limit</p>
						<p class="tvalue aright"><?php echo display::display_value($lower_limit, 1, $iflag) ?></p>
					</div>
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Upper Limit</p>
						<p class="tvalue aright"><?php echo display::display_value($upper_limit, 1, $iflag) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Lowest Weight</p>
						<p class="tvalue aright"><?php echo display::display_value(tblweeklydata::getMinMaxValue("weight", $first_date, false, true), 1, $iflag) ?></p>
					</div>
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Highest Weight</p>
						<p class="tvalue aright"><?php echo display::display_value(tblweeklydata::getMinMaxValue("weight", $first_date, false, false), 1, $iflag) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Weekly Weight Change</p>
						<p class="tvalue aright"><?php echo display::display_change(display::getDifference("weight", $weight_start_report_date, $weight_end_report_date), false, $iflag) ?></p>
					</div>
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Total Weight Change</p>
						<p class="tvalue aright"><?php echo display::display_change(display::getDifference("weight", $first_date, false), false, $iflag) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Affordable Weight Loss</p>
						<p class="tvalue aright"><?php echo display::display_value(display::affordableLossGain($current_weight, $lower_limit, $upper_limit), 1, $iflag) ?></p>
					</div>
					<div class="dcon twocols">
						<p class="tlabel">&nbsp; Affordable Weight Gain</p>
						<p class="tvalue aright"><?php echo display::display_value(display::affordableLossGain($current_weight, $lower_limit, $upper_limit, true), 1, $iflag) ?></p>
					</div>
				</div>
			</div>
			<div id="dother" class="hdata">
				<header class="thead">Summary Of Other Statistics</header>
				<div class="drow">
					<div class="dcon twocols col1">
						<p class="tlabel">&nbsp; Current Blood Pressure</p>
						<p class="tvalue aleft"><?php echo $blood_pressure->display(); ?></p>
					</div>
					<div class="dcon twocols col2">
						<p class="tlabel">&nbsp; Blood Pressure Category</p>
						<p class="tvalue aleft"><?php echo $blood_pressure->getCategory(); ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon twocols col1">
						<p class="tlabel">&nbsp; Body Mass Index (kg/m&sup2;)</p>
						<p class="tvalue aleft"><?php echo number_format(composition::body_mass_index($current_weight, $height), 2) ?></p>
					</div>
					<div class="dcon twocols col2">
						<p class="tlabel">&nbsp; Body Mass Index Category</p>
						<p class="tvalue aleft"><?php echo composition::bmi_category(composition::body_mass_index($current_weight, $height), $gflag) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon twocols col1">
						<p class="tlabel">&nbsp; Lean Body Mass</p>
						<p class="tvalue aleft"><?php echo display::display_value($lbm, 1, $iflag) ?></p>
					</div>
					<div class="dcon twocols col2">
						<p class="tlabel">&nbsp; Body Fat Mass</p>
						<p class="tvalue aleft"><?php echo display::display_value($bfm, 1, $iflag) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon twocols col1">
						<p class="tlabel">&nbsp; Body Fat Percentage</p>
						<p class="tvalue aleft"><?php echo number_format(100 * composition::fat_percentage($current_weight, $bfm), 2) . '%' ?></p>
					</div>
					<div class="dcon twocols col2">
						<p class="tlabel">&nbsp; Body Fat Percentage Category</p>
						<p class="tvalue aleft"><?php echo composition::body_fat_category(composition::fat_percentage($current_weight, $bfm), $age, $gflag) ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon twocols col1">
						<p class="tlabel">&nbsp; Basal Metabolic Rate</p>
						<p class="tvalue aleft"><?php echo number_format(composition::basal_metabolic_rate($lbm), 2, '.', '') ?></p>
					</div>
					<div class="dcon twocols col2">
						<p class="tlabel">&nbsp; Resting Metabolic Rate</p>
						<p class="tvalue aleft"><?php echo number_format(composition::resting_metabolic_rate($lbm), 2, '.', '') ?></p>
					</div>
				</div>
				<div class="drow">
					<div class="dcon twocols col1">
						<p class="tlabel">&nbsp; Waist To Height Ratio</p>
						<p class="tvalue aleft"><?php echo number_format(composition::waist2height($current_waist_size, $height), 2) . '%' ?></p>
					</div>
				</div>
			</div>
		</section>
		<section id="scharts">
			<?php
				for($i = 1; $i <= 2; $i++) {
					?>
			<div class="shchart">
			<?php
					echo '<canvas id="s' . ($i == 1 ? $data_const1["canvas"][$i] : $data_const2["canvas"][$i]) . '" width="714" height="466"></canvas>';
			?>
			</div><?php
				}
			?>
		</section>
		<footer id="buttons">
			<a class="button" href="includes/display_steps.php">Step Counts</a>
			<a class="button" href="includes/display_exercise.php">Fitness Classes</a>
			<a class="button" href="includes/display_cardio.php">Cardio Exercises</a>
			<a class="button" href="includes/display_strength.php">Strength Exercises</a>
			<a class="button" href="includes/display_data.php">Weight/Waist Records</a>
			<a class="button" href="includes/display_medical.php">Blood Pressure Records</a>
			<a class="button" href="includes/report.php" target="_blank">Generate PDF</a>
			<a class="button" href="includes/logout.php">Log Out</a>
		</footer>
	</body>
</html>
