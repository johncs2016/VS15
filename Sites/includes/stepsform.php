<?php
	include_once "sessions.php";
	include("steps.php");
	include("activities.php");
	include("validator.php");
	include "display_functions.php";
	include "composition.php";
	include "body_measurements.php";
	require_once("load_libraries.php");
	
	$userid = $_SESSION['userid'];	
	$user = tblusers::get($userid);
	$bm_object = new bodyMeasurements($userid);
	$stepsize = $bm_object->getStepSize();
	$isimperial = $user->getIsImperial();
	$activities = tblactivities::getAll();
	$addrec = !isset($_GET['id']);
	$now = new DateTime(date("Y-m-d"));
	if (!empty($_POST)) {
		$input = array(
			'odate'			=>	DateTime::createFromFormat('d/m/Y', $_POST['odate']),
			'numberofstepswalked'	=>	$_POST['steps'],
		);
	}
	else {
		$object = ($addrec ? null : tblstepscounts::get($_GET['id']));
		$input = array(
			'odate'			=>	($addrec ? $now : $object->getObsDate()),
			'numberofstepswalked'	=>	($addrec ? null : $object->getStepsCount()),
		);
	}

	if (!empty($_POST)) {

		$action = (isset($_GET['id']) ? 'updated' : 'added');

		try {
			$properties = array(
				'_userid'		=>	$userid,
				'_obsdate'		=>	$_POST['odate'],
				'_numberofstepswalked'	=>	$_POST['steps'],
			);
			
			if (empty($properties['_numberofstepswalked'])) {
				$properties['_numberofstepswalked'] = 0;
			}

			$val_object = new validator();
			$valid = $val_object->validate_date("obsdate", $properties["_obsdate"]);
			$valid = ($valid && $val_object->validate_integer("numberofstepswalked", $properties["_numberofstepswalked"]));
			$error_array = $val_object->getAllErrors();
			
			if ($valid) {
				if(isset($_GET['id'])) $properties['_id'] = $_GET['id'];
				if($action == 'added') {
					$object = tblstepscounts::create($properties);
				} else {
					$object = tblstepscounts::get($_GET['id']);
					$object->update($properties);
				}
				$action = 'record has been ' . $action . '.';
				header("Location: display_steps.php?action=" . $action);
				exit();
			}
		}
		catch (PDOException $e) {
			$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
			header("Location: display_steps.php?action=" . $action);
			exit();
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<title>Steps Count Form</title>
		<?php
			loadJavascript("../scripts/loader.js");
			loadJQuery();
		?>
		<script>
			loadCSS("../css/styles.css");
			require("../scripts/display_values.js");

			function displayDistance() {
				var steps = document.getElementById("steps").value;
				var size = <?php echo $stepsize; ?>;
				var isimp = $('#isimp').is(':checked');
				var dist = steps * size / 1000;

				if (steps === ""){
					document.getElementById("distlabel").innerHTML="";
					document.getElementById("distdata").innerHTML="";
				} else {
					document.getElementById("distlabel").innerHTML="Distance Walked";
					document.getElementById("distdata").innerHTML = display_value(dist, 2, isimp, true);
				}
			}

			jQuery(function($){ //on document.ready
				displayDistance();
				$("#steps").focus();
				$('input[type=date]').on('click', function(event) {
    					event.preventDefault();
				});
        		$('#odate').datepicker({ dateFormat: "dd/mm/yy", showButtonPanel: true }).val();
    		})
		</script>
	</head>
	<body>
		<div id='dhead'>
			<h2><?php echo ($addrec ? "Add New" : "Edit") . " Steps Count Record For " . $_SESSION['name']; ?></h2>
			<span class="required_notification">* Denotes Required Field</span>
		</div>
		<div id='fcon'>
			<form class="data_form" action="<?php echo htmlentities($_SERVER['PHP_SELF']).($addrec ? '' : '?id='.$_GET['id']); ?>" method="post">
				<p class="icheck">
					<input type="checkbox" id="isimp" name="isimp" value="Yes" onclick="displayDistance()"<?php echo ($isimperial ? " checked" : ""); ?>>Display distances in miles
				</p>
				<p class="dpicker">
					<label for="odate">Observation Date</label>
					<input type="text" id="odate" name="odate" value="<?php echo $input['odate']->format('d/m/Y'); ?>" placeholder="<?php echo $now->format('d/m/Y'); ?>">
					<span class="<?php echo isset($error_array['obsdate']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['obsdate']) ? $error_array['obsdate'] : '* Please select a date'; ?></span>
				</p>
				<p class="tbox">
					<label for="steps">Steps Count</label>
					<input type="text" name="steps" id="steps" value = "<?php echo $input['numberofstepswalked']; ?>" placeholder="0" onchange="displayDistance()">
					<span class="<?php echo isset($error_array['activity']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['numberofstepswalked']) ? $error_array['numberofstepswalked'] : 'Please enter the steps count'; ?></span>
				</p>
				<div class="dcon">
					<p id="distlabel" class="tlabel"></p>
					<p id="distdata" class="tdata"></p>
				</div>
				<p class="submit">
					<a href="display_steps.php" class="button">Back To Results</a>
					<button type="submit" id="submit" class="button"><?php echo ($addrec ? 'Add' : 'Update'); ?> Record</button>

				</p>
			</form>
		</div>
	</body>
</html>