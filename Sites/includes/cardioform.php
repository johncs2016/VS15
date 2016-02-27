<?php
	include_once "sessions.php";
	include "cardio.php";
	include "cardioequipment.php";
	include "cardioprogs.php";
	include "venues.php";
	include "exercise.php";
	include "validator.php";
	require_once "load_libraries.php";
	
	$userprogs = tblcardiouserprogs::getAll();
	$userprogs_array = array();
	foreach ($userprogs as $key => $userprog) {
		$userprogs_array[$key] = array(
			'id'		=>	$userprog->getID(),
			'userid'	=>	$userprog->getUserID(),
			'equipid'	=>	$userprog->getEquipmentID(),
			'level'		=>	$userprog->getLevel(),
			'progid'	=>	$userprog->getProgID(),
			'minutes'	=>	$userprog->getMinutes(),
			'speed'		=>	$userprog->getSpeed(),
			'incline'	=>	$userprog->getIncline()
		);
	}
	$userprogs_json = json_encode($userprogs_array);
	
	$userid = $_SESSION['userid'];	
	$user = tblusers::get($userid);
	$venues = tblvenues::getAll();
	$equipment = tblcardioequipment::getAll();
	$programs = tblcardioprogs::getAll();
	$addrec = !isset($_GET['id']);
	$now = new DateTime(date("Y-m-d"));
	if (!empty($_POST)) {
		$input = array(
			'odate'			=>	DateTime::createFromFormat('d/m/Y', $_POST['odate']),
			'venueid'		=>	$_POST['venue'],
			'equipid'		=>	$_POST['equip'],
			'level'			=>	$_POST['level'],
			'progid'		=>	$_POST['prog'],
			'minutes'		=>	$_POST['minutes'],
			'speed'			=>	$_POST['speed'],
			'incline'		=>	$_POST['incline'],
			'distance'		=>	$_POST['distance'],
			'calories'		=>	$_POST['calories']
		);
	}
	else {
		$object = ($addrec ? null : tblcardio::get($_GET['id']));
		$input = array(
			'odate'			=>	($addrec ? $now : $object->getObsDate()),
			'venueid'		=>	($addrec ? 1 : $object->getVenueID()),
			'equipid'		=>	($addrec ? 1 : $object->getEquipmentID()),
			'level'			=>	($addrec ? null : $object->getLevel()),
			'progid'		=>	($addrec ? 0 : $object->getProgID()),
			'minutes'		=>	($addrec ? null : $object->getMinutes()),
			'speed'			=>	($addrec ? null : $object->getSpeed()),
			'incline'		=>	($addrec ? null : $object->getIncline()),
			'distance'		=>	($addrec ? null : $object->getDistance()),
			'calories'		=>	($addrec ? 0 : $object->getCalories())
		);
	}

	$equip = tblcardioequipment::get($input['equipid']);
	$isKM = ($equip == null ? false : $equip->getKM());
	
	if (!empty($_POST)) {

		$action = (isset($_GET['id']) ? 'updated' : 'added');

		try {
			$properties = array(
				'_userid'		=>	$userid,
				'_obsdate'		=>	$_POST['odate'],
				'_venueid'		=>	$_POST['venue'],
				'_equipid'		=>	$_POST['equip'],
				'_level'		=>	$_POST['level'],
				'_progid'		=>	$_POST['prog'],
				'_minutes'		=>	$_POST['minutes'],
				'_speed'		=>	$_POST['speed'],
				'_incline'		=>	$_POST['incline'],
				'_distance'		=>	$_POST['distance'],
				'_calories'		=>	$_POST['calories']
			);
			
			if (empty($properties['_level'])) {
				$properties['_level'] = 0;
			}

			if (empty($properties['_minutes'])) {
				$properties['_minutes'] = 0;
			}

			if (empty($properties['_speed'])) {
				$properties['_speed'] = 0;
			}

			if (empty($properties['_incline'])) {
				$properties['_incline'] = 0;
			}

			if (empty($properties['_distance'])) {
				$properties['_distance'] = 0;
			}

			if (empty($properties['_calories'])) {
				$properties['_calories'] = 0;
			}

			$val_object = new validator();
			$valid = $val_object->validate_date("obsdate", $properties["_obsdate"]);
			$valid = ($valid && $val_object->validate_integer("level", $properties["_level"]));
			$valid = ($valid && $val_object->validate_integer("minutes", $properties["_minutes"]));
			$valid = ($valid && $val_object->validate_float("speed", $properties["_speed"]));
			$valid = ($valid && $val_object->validate_float("incline", $properties["_incline"]));
			$valid = ($valid && $val_object->validate_float("distance", $properties["_distance"]));
			$valid = ($valid && $val_object->validate_integer("calories", $properties["_calories"]));
			$error_array = $val_object->getAllErrors();
			
			$venue = tblvenues::get($properties['_venueid']);
			if ($venue == null) {
				if (isset($error_array['venue'])) $error_array['venue'] .= "\n"; else $error_array['venue'] = '';
				$error_array['venue'] .= "Venue Does Not Exist";
				$valid = false;
			}
			
			if ($equip == null) {
				if (isset($error_array['equip'])) $error_array['equip'] .= "\n"; else $error_array['equip'] = '';
				$error_array['equip'] .= "Equipment Does Not Exist";
				$valid = false;
			} else {
			}
			
			$prog = tblcardioprogs::get($properties['_progid']);
			if ($prog == null) {
				if (isset($error_array['prog'])) $error_array['prog'] .= "\n"; else $error_array['prog'] = '';
				$error_array['prog'] .= "Program Does Not Exist";
				$valid = false;
			}
			
			if ($valid) {
				if(isset($_GET['id'])) $properties['_id'] = $_GET['id'];
				if($action == 'added') {
					$object = tblcardio::create($properties);
				} else {
					$object = tblcardio::get($_GET['id']);
					$object->update($properties);
				}
				$action = 'record has been ' . $action . '.';
				header("Location: display_cardio.php?action=" . $action);
				exit();
			}
		}
		catch (PDOException $e) {
			$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
			header("Location: display_cardio.php?action=" . $action);
			exit();
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<title>Cardio Exercise Form</title>
		<?php
			loadJavascript("../scripts/loader.js");
			loadJQuery();
		?>
		<script>
			loadCSS("../css/styles.css");

			function userprog(id) {
				var userprogs = <?php echo $userprogs_json; ?>;
				for (var i = 0; i < userprogs.length; i++) {
					if (id == userprogs[i].equipid) return userprogs[i];
				}
				return false;
			}
			
			function distance(speed, minutes) {
				var v = Number(speed);
				var t = Number(minutes) / 60;
				var s = v * t;
				return (s == 0 ? '' : s.toFixed(2));
			}
			
			function setDistance() {
				var equip = $('#equip').val();
				var e = Number(equip);
				var speed = $('#speed').val();
				var no_speed = (speed == '' || Number(speed) == 0 || speed == 0);
				if(no_speed) speed = '0';
				var minutes = $('#minutes').val();
				var no_minutes = (minutes == '' || Number(minutes) == 0 || minutes == 0);
				if(no_minutes) minutes = '0';
				$('#distance').val((e == 1 ? distance(speed, minutes) : ''));
			}

			function setDefault() {
				var equip = $('#equip').val();
				var e = Number(equip);
				var up = userprog(equip);
				var addrec = <?php echo ($addrec == true ? 1 : 0); ?>;
				if (addrec == 1) {
					$('#level').val(up.level);
					$('#prog').val(up.progid);
					$('#minutes').val(up.minutes);
					$('#speed').val(up.speed);
					$('#incline').val(up.incline);
					setDistance();
					$('#' + (e == 1 ? 'calories' : 'distance')).focus();
				}
			}

			jQuery(function($){ //on document.ready
				setDefault();
				$('input[type=date]').on('click', function(event) {
    					event.preventDefault();
				});
        		$('#odate').datepicker({ dateFormat: "dd/mm/yy", showButtonPanel: true }).val();
				$('#equip').change(setDefault);
				$('#speed').change(setDistance);
				$('#minutes').change(setDistance);
			});
		</script>
	</head>
	<body>
		<div id='dhead'>
			<h2><?php echo ($addrec ? "Add New" : "Edit") . " Cardio Exercise Record For " . $_SESSION['name']; ?></h2>
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
					<label for="equip">Equipment</label>
					<select name="equip" id="equip">
						<?php
							foreach ($equipment as $object) {
								$id = $object->getID();
								echo '<option ' . (($id == $input['equipid']) ? 'selected ' : '') . 'value="'.$id.'">' . $object->getName() . '</option>';
							}
						?>
					</select>
					<span class="form_hint">* Please Select a piece of equipment from the drop-down list</span>
				</p>
				<p class="tbox">
					<label for="level">Level</label>
					<input style="width:40px" type="text" name="level" id="level" value="<?php echo ($input['level'] == null ? null : number_format($input['level'], 0)); ?>">
					<span class="<?php echo isset($error_array['level']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['level']) ? $error_array['level'] : 'Please enter a valid level'; ?></span>
				</p>
				<p class="tselect">
					<label for="prog">Programme</label>
					<select name="prog" id="prog">
						<?php
							foreach ($programs as $object) {
								$id = $object->getID();
								if ($id == "") $id = 0;
								echo "<p>$id</p>";
								echo '<option ' . (($id == $input['progid']) ? 'selected ' : '') . 'value="'.$id.'">' . $object->getProgram() . '</option>';
							}
						?>
					</select>
					<span class="form_hint">* Please Select a program from the drop-down list</span>
				</p>
				<p class="tbox">
					<label for="minutes">Time in Minutes</label>
					<input style="width:40px" type="text" name="minutes" id="minutes" value="<?php echo ($input['minutes'] == null ? null : number_format($input['minutes'], 0)); ?>" placeholder="0">
					<span class="<?php echo isset($error_array['minutes']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['minutes']) ? $error_array['minutes'] : 'Please enter a valid number of minutes'; ?></span>
				</p>
				<p class="tbox">
					<label for="speed">Speed</label>
					<input style="width:40px" type="text" name="speed" id="speed" value="<?php echo ($input['speed'] == null ? null : number_format($input['speed'], 1)); ?>">
					<span class="<?php echo isset($error_array['speed']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['speed']) ? $error_array['speed'] : 'Please enter a valid speed'; ?></span>
				</p>
				<p class="tbox">
					<label for="incline">Incline</label>
					<input style="width:40px" type="text" name="incline" id="incline" value="<?php echo ($input['incline'] == null ? null : number_format($input['incline'], 1)); ?>">
					<span class="<?php echo isset($error_array['incline']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['incline']) ? $error_array['incline'] : 'Please enter a valid incline'; ?></span>
				</p>
				<p class="tbox">
					<label for="distance">Distance</label>
					<input style="width:80px" type="text" name="distance" id="distance" value="<?php echo ($input['distance'] == null ? null : number_format($input['distance'], ($isKM == false ? 0 : 2))); ?>">
					<span class="<?php echo isset($error_array['distance']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['distance']) ? $error_array['speed'] : 'Please enter a valid distance'; ?></span>
				</p>
				<p class="tbox">
					<label for="calories">Calories</label>
					<input style="width:40px" type="text" name="calories" id="calories" value="<?php echo ($input['calories'] == null ? null : number_format($input['calories'], 0)); ?>">
					<span class="<?php echo isset($error_array['calories']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['calories']) ? $error_array['calories'] : 'Please enter a valid number of calories'; ?></span>
				</p>
				<p class="submit">
					<a href="display_cardio.php" class="button">Back To Results</a>
					<button type="submit" id="submit" class="button"><?php echo ($addrec ? 'Add' : 'Update'); ?> Record</button>

				</p>
			</form>
		</div>
	</body>
</html>