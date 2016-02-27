<?php
	include_once "sessions.php";
	include("activities.php");
	include("exercise.php");
	include("venues.php");
	include("validator.php");
	require_once("load_libraries.php");
	
	$userid = $_SESSION['userid'];	
	$user = tblusers::get($userid);
	$activities = tblactivities::getAll();
	$venues = tblvenues::getAll();
	$addrec = !isset($_GET['id']);
	$now = new DateTime(date("Y-m-d"));
	if (!empty($_POST)) {
		$input = array(
			'odate'			=>	DateTime::createFromFormat('d/m/Y', $_POST['odate']),
			'venueid'		=>	$_POST['venue'],
			'activityid'	=>	$_POST['activity']
		);
	}
	else {
		$object = ($addrec ? null : tblexercise::get($_GET['id']));
		$input = array(
			'odate'			=>	($addrec ? $now : $object->getObsDate()),
			'venueid'			=>	($addrec ? 1 : $object->getVenueID()),
			'activityid'	=>	($addrec ? 2 : $object->getActivityID())
		);
	}
	
	// $todays_activities = tblexercise::getByDate($input['odate'], $input['odate']);

	if (!empty($_POST)) {

		$action = (isset($_GET['id']) ? 'updated' : 'added');

		try {
			$properties = array(
				'_userid'		=>	$userid,
				'_obsdate'		=>	$_POST['odate'],
				'_venueid'		=>	$_POST['venue'],
				'_activityid'	=>	$_POST['activity']
			);
			
			$val_object = new validator();
			$valid = $val_object->validate_date("obsdate", $properties["_obsdate"]);
			$error_array = $val_object->getAllErrors();
			
			$venue = tblvenues::get($properties['_venueid']);
			if ($venue == null) {
				if (isset($error_array['venueid'])) $error_array['venueid'] .= "\n"; else $error_array['venueid'] = '';
				$error_array['venue'] .= "Venue Does Not Exist";
				$valid = false;
			}
			
			$activity = tblactivities::get($properties['_activityid']);
			if ($activity == null) {
				if (isset($error_array['activityid'])) $error_array['activityid'] .= "\n"; else $error_array['activityid'] = '';
				$error_array['activity'] .= "Activity Does Not Exist";
				$valid = false;
			}
			
			if ($valid) {
				if(isset($_GET['id'])) $properties['_id'] = $_GET['id'];
				if($action == 'added') {
					$object = tblexercise::create($properties);
				} else {
					$object = tblexercise::get($_GET['id']);
					$object->update($properties);
				}
				$action = 'record has been ' . $action . '.';
				header("Location: display_exercise.php?action=" . $action);
				exit();
			}
		}
		catch (PDOException $e) {
			$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
			header("Location: display_exercise.php?action=" . $action);
			exit();
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<title>Fitness Classes Form</title>
		<?php
			loadJavascript("../scripts/loader.js");
			loadJQuery();
		?>
		<script>
			loadCSS("../css/styles.css");

			jQuery(function($){ //on document.ready
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
			<h2><?php echo ($addrec ? "Add New" : "Edit") . " Fitness Class Record For " . $_SESSION['name']; ?></h2>
			<span class="required_notification">* Denotes Required Field</span>
		</div>
		<div id='fcon'>
			<form class="data_form" action="<?php echo htmlentities($_SERVER['PHP_SELF']).($addrec ? '' : '?id='.$_GET['id']); ?>" method="post">
				<p class="dpicker">
					<label for="odate">Observation Date</label>
					<input type="text" id="odate" name="odate" value="<?php echo $input['odate']->format('d/m/Y'); ?>" placeholder="<?php echo $now->format('d/m/Y'); ?>">
					<span class="<?php echo isset($error_array['obsdate']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['obsdate']) ? $error_array['obsdate'] : '* Please select a date'; ?></span>
				</p>
				<p class="tselect">
					<label for="venue">Venue</label>
					<select name="venue" id="venue">
						<?php
							foreach ($venues as $object) {
								$id = $object->getID();
								echo '<option ' . (($id == $input['venueid']) ? 'selected ' : '') . 'value="'.$id.'">' . $object->getVenue() . '</option>';
							}
						?>
					</select>
					<span class="form_hint">* Please Select an venue from the drop-down list</span>
				</p>
				<p class="tselect">
					<label for="activity">Activity</label>
					<select name="activity" id="activity">
						<?php
							foreach ($activities as $object) {
								if($object->getShowActivity()) {
									$id = $object->getID();
									echo '<option ' . (($id == $input['activityid']) ? 'selected ' : '') . 'value="'.$id.'">' . $object->getactivity() . '</option>';
								}
							}
						?>
					</select>
					<span class="form_hint">* Please Select a fitness class from the drop-down list</span>
				</p>
				<p class="submit">
					<a href="display_exercise.php" class="button">Back To Results</a>
					<button type="submit" id="submit" class="button"><?php echo ($addrec ? 'Add' : 'Update'); ?> Record</button>

				</p>
			</form>
		</div>
	</body>
</html>