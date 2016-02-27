<?php
	include_once("sessions.php");
	require_once("load_libraries.php");
	require_once("medical.php");
	require_once("validator.php");
	
	$id = (isset($_GET['id']) ? $_GET['id'] : false);
	$userid = $_SESSION['userid'];
	$user = tblusers::get($userid);
	$addrec = !isset($_GET['id']);
	$now = new DateTime();
	if (!empty($_POST)) {
		$input = array(
			'odate'		=>	DateTime::createFromFormat('d/m/Y', $_POST['odate']),
			'systolic'	=>	$_POST['systolic'],
			'diastolic'	=>	$_POST['diastolic'],
			'pulse'		=>	$_POST['pulse']
		);
	}
	else {
		$object = ($addrec ? null : tblmedical::get($_GET['id']));
		$input = array(
			'odate'		=>	($addrec ? $now : $object->getObsDate()),
			'systolic'	=>	($addrec ? null : $object->getSystolic()),
			'diastolic'	=>	($addrec ? null : $object->getDiastolic()),
			'pulse'		=>	($addrec ? null : $object->getPulse())
		);
	}

	if (!empty($_POST)) {

		$action = (isset($_GET['id']) ? 'updated' : 'added');

		try {
			$properties = array(
				'_userid'		=>	$userid,
				'_obsdate'		=>	$_POST['odate'],
				'_systolic'		=>	$_POST['systolic'],
				'_diastolic'	=>	$_POST['diastolic'],
				'_pulse'		=>	$_POST['pulse']
			);

			if (empty($properties['_systolic'])) {
				$properties['_systolic'] = 0;
			}

			if (empty($properties['_diastolic'])) {
				$properties['_diastolic'] = 0;
			}

			if (empty($properties['_pulse'])) {
				$properties['_pulse'] = 0;
			}

			$val_object = new validator();
			$valid = $val_object->validate_date("obsdate", $properties["_obsdate"]);
			$valid = ($valid && $val_object->validate_integer("systolic", $properties["_systolic"]));
			$valid = ($valid && $val_object->validate_integer("diastolic", $properties["_diastolic"]));
			$valid = ($valid && $val_object->validate_integer("pulse", $properties["_pulse"]));
			$error_array = $val_object->getAllErrors();
			
			if (empty($error_array)) {
				if(isset($_GET['id'])) $properties['_id'] = $_GET['id'];
				if($action == 'added') {
					$object = tblmedical::create($properties);
				} else {
					$object = tblmedical::get($_GET['id']);
					$object->update($properties);
				}
				$action = 'record has been ' . $action . '.';
				header("Location: display_medical.php?action=" . $action);
				exit();
			}
		}
		catch (PDOException $e) {
			$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
			header("Location: display_medical.php?action=" . $action);
			exit();
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<title>Blood Pressure Entry Form</title>
		<?php
			loadJQuery();
			loadJavascript("../scripts/loader.js");
		?>
		<script>
			loadCSS("../css/styles.css");
			loadCSS("../css/list.css");

			jQuery(function($){ //on document.ready
				$('input[type=date]').on('click', function(event) {
				event.preventDefault();
				});
				$('#odate').datepicker({ dateFormat: "dd/mm/yy", showButtonPanel: true }).val();
				$('#systolic').focus();
			});
		</script>
	</head>
	<body>
		<div id='dhead'>
			<h2><?php echo ($addrec ? "Add New" : "Edit") . " Blood Pressure Record For " . $_SESSION['name']; ?></h2>
			<span class="required_notification">* Denotes Required Field</span>
		</div>
		<div id='fcon'>
			<form class="data_form" action="<?php echo htmlentities($_SERVER['PHP_SELF']).($addrec ? '' : '?id='.$_GET['id']); ?>" method="post">
				<p class="dpicker">
					<label for="odate">Observation Date</label>
					<input type="text" id="odate" name="odate" value="<?php echo ($input['odate'] == null ? '' : $input['odate']->format('d/m/Y')); ?>" placeholder="<?php echo $now->format('d/m/Y'); ?>">
					<span class="<?php echo isset($error_array['obsdate']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['obsdate']) ? $error_array['obsdate'] : '* Please select a date'; ?></span>
				</p>
				<p class="tbox">
					<label for="systolic">Systolic Blood Pressure</label>
					<input style="width:40px" type="text" name="systolic" id="systolic" value="<?php echo ($input['systolic'] == null ? null : number_format($input['systolic'], 0)); ?>" placeholder="0">
					<span class="<?php echo isset($error_array['systolic']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['systolic']) ? $error_array['systolic'] : 'Please enter a valid systolic blood pressure'; ?></span>
				</p>
				<p class="tbox">
					<label for="diastolic">Diastolic Blood Pressure</label>
					<input style="width:40px" type="text" name="diastolic" id="diastolic" placeholder="0" value="<?php echo ($input['diastolic'] == null ? null : number_format($input['diastolic'], 0)); ?>">
					<span class="<?php echo isset($error_array['diastolic']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['diastolic']) ? $error_array['diastolic'] : 'Please enter a valid diastolic blood pressure'; ?></span>
				</p>
				<p class="tbox">
					<label for="pulse">Pulse</label>
					<input style="width:40px" type="text" name="pulse" id="pulse" placeholder="0" value="<?php echo ($input['pulse'] == null ? null : number_format($input['pulse'], 0)); ?>">
					<span class="<?php echo isset($error_array['pulse']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['pulse']) ? $error_array['pulse'] : 'Please enter a valid pulse'; ?></span>
				</p>
				<p class="submit">
					<a href="display_medical.php" class="button">Back To Results</a>
					<button type="submit" id="submit" class="button"><?php echo ($addrec ? 'Add' : 'Update'); ?> Record</button>
			<a class="button" href="logout.php">Log Out</a>
				</p>
			</form>
		</div>
	</body>
</html>
