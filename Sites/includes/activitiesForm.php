<?php
	include_once("sessions.php");
	include("activities.php");
	include("validator.php");
	require_once("load_libraries.php");

	$addrec = !isset($_GET['id']);
	$now = new DateTime();
	if (!empty($_POST)) {
		$input = array(
			'activity'		=>	$_POST['activity'],
			'met'			=>	$_POST['met'],
			'duration'		=>	$_POST['duration'],
			'equivalentsteps'	=>	$_POST['equivalentsteps']
		);
	}
	else {
		$object = ($addrec ? null : tblactivities::get($_GET['id']));
		$input = array(
			'activity'		=>	($addrec ? null : $object->getactivity()),
			'met'			=>	($addrec ? null : $object->getMET()),
			'duration'		=>	($addrec ? null : $object->getDuration()),
			'equivalentsteps'	=>	($addrec ? null : $object->getEquivalentSteps())
		);
	}

	if (!empty($_POST)) {

		$action = (isset($_GET['id']) ? 'updated' : 'added');

		try {
			$properties = array(
				'_userid'			=>	$_SESSION['userid'],
				'_activity'			=>	$_POST['activity'],
				'_met'				=>	$_POST['met'],
				'_duration'			=>	$_POST['duration'],
				'_equivalentsteps'		=>	$_POST['equivalentsteps']
			);
			
			$val_object = new validator();
			$valid = $val_object->validate_string("activity", $properties["_activity"], true, 25);
			$valid = ($valid && $val_object->validate_float("met", $properties["_met"]));
			$valid = ($valid && $val_object->validate_integer("duration", $properties["_duration"]));
			$valid = ($valid && $val_object->validate_integer("equivalentsteps", $properties["_equivalentsteps"]));
			$error_array = $val_object->getAllErrors();
						
			if ($valid) {
				if(isset($_GET['id'])) $properties['_id'] = $_GET['id'];
				if($action == 'added') {
					$object = tblactivities::create($properties);
				} else {
					$object = tblactivities::get($_GET['id']);
					$object->update($properties);
				}
				$action = 'record has been ' . $action . '.';
				header("Location: display_steps.php?action=" . $action);
				exit();
			}
		}
		catch (PDOException $e) {
			$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
			header("Location: display_activities.php?action=" . $action);
			exit();
		}
	}
	
	ob_flush();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<title>Activities Form</title>
		<?php
			loadCSS("../css/styles.css");
			loadJQuery();
			loadJavascript("../scripts/loader.js");
		?>
		<script>
			jQuery(function($){ //on document.ready
				$('#activity').focus();
			});
		</script>
	</head>
	<body>
		<div id='dhead'>
			<h2><?php echo ($addrec ? "Add New" : "Edit") . " Activities Record For " . $_SESSION['name']; ?></h2>
			<span class="required_notification">* Denotes Required Field</span>
		</div>
		<div id='fcon'>
			<form class="data_form" action="<?php echo htmlentities($_SERVER['PHP_SELF']).($addrec ? '' : '?id='.$_GET['id']); ?>" method="post">
				<p class="tbox">
					<label for="activity">Activity</label>
					<input type="text" name="activity" id="activity" value = "<?php echo $input['activity']; ?>" placeholder="Please enter an activity">
					<span class="<?php echo isset($error_array['activity']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['activity']) ? $error_array['activity'] : 'Please enter an activity'; ?></span>
				</p>
				<p class="tbox">
					<label for="met">MET</label>
					<input type="text" name="met" id="met" value = "<?php echo $input['met']; ?>" placeholder="1">
					<span class="<?php echo isset($error_array['met']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['met']) ? $error_array['met'] : 'Please enter a MET'; ?></span>
				</p>
				<p class="tbox">
					<label for="duration">Duration</label>
					<input type="text" name="duration" id="duration" value = "<?php echo $input['duration']; ?>" placeholder="0">
					<span class="<?php echo isset($error_array['duration']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['duration']) ? $error_array['duration'] : 'Please enter a duration'; ?></span>
				</p>
				<p class="tbox">
					<label for="equivalentsteps">Equivalent Steps</label>
					<input type="text" name="equivalentsteps" id="equivalentsteps" value = "<?php echo $input['equivalentsteps']; ?>" placeholder="0">
					<span class="<?php echo isset($error_array['equivalentsteps']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['equivalentsteps']) ? $error_array['equivalentsteps'] : 'Please enter equivalent steps'; ?></span>
				</p>
				<p class="submit">
					<a href="display_activities.php" class="button">Back To Results</a>
					<button type="submit" id="submit" class="button"><?php echo ($addrec ? 'Add' : 'Update'); ?> Record</button>
			<a class="button" href="logout.php">Log Out</a>

				</p>
			</form>
		</div>
	</body>
</html>
