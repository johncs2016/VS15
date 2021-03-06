<?php
	include_once "sessions.php";
	include "strengthequipment.php";
	include "strengthprogs.php";
	include "exercise.php";
	include "validator.php";
	require_once "load_libraries.php";
	
	$userid = $_SESSION['userid'];	
	$user = tblusers::get($userid);
	$equipment = tblstrengthequipment::getAll();
	$addrec = !isset($_GET['id']);
	$now = new DateTime(date("Y-m-d"));
	if (!empty($_POST)) {
		$input = array(
			'equipid'	=>	$_POST['equip'],
			'weight'		=>	$_POST['weight'],
			'sets'		=>	$_POST['sets'],
			'reps'		=>	$_POST['reps']
		);
	}
	else {
		$object = ($addrec ? null : tblstrengthprogs::get($_GET['id']));
		$input = array(
			'equipid'		=>	($addrec ? 1 : $object->getEquipmentID()),
			'weight'		=>	($addrec ? null : $object->getWeight()),
			'sets'			=>	($addrec ? null : $object->getSets()),
			'reps'			=>	($addrec ? null : $object->getReps())
		);
	}

	$equip = tblstrengthequipment::get($input['equipid']);
	
	if (!empty($_POST)) {

		$action = (isset($_GET['id']) ? 'updated' : 'added');

		try {
			$properties = array(
				'_userid'	=>	$userid,
				'_equipid'	=>	$_POST['equip'],
				'_weight'	=>	$_POST['weight'],
				'_sets'		=>	$_POST['sets'],
				'_reps'		=>	$_POST['reps']
			);
			
			if (empty($properties['_weight'])) {
				$properties['_weight'] = 0;
			}

			if (empty($properties['_sets'])) {
				$properties['_sets'] = 0;
			}

			if (empty($properties['_reps'])) {
				$properties['_reps'] = 0;
			}

			$val_object = new validator();
			$valid = $val_object->validate_integer("weight", $properties["_weight"]);
			$valid = ($valid && $val_object->validate_integer("sets", $properties["_sets"]));
			$valid = ($valid && $val_object->validate_integer("reps", $properties["_reps"]));
			$error_array = $val_object->getAllErrors();
			
			if ($equip == null) {
				if (isset($error_array['equip'])) $error_array['equip'] .= "\n"; else $error_array['equip'] = '';
				$error_array['equip'] .= "Equipment Does Not Exist";
				$valid = false;
			}
			
			if ($valid) {
				if(isset($_GET['id'])) $properties['_id'] = $_GET['id'];
				if($action == 'added') {
					$object = tblstrengthprogs::create($properties);
				} else {
					$object = tblstrengthprogs::get($_GET['id']);
					$object->update($properties);
				}
				$action = 'record has been ' . $action . '.';
				header("Location: display_strength_progs.php?action=" . $action);
				exit();
			}
		}
		catch (PDOException $e) {
			$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
			header("Location: display_strength_progs.php?action=" . $action);
			exit();
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<title>Strength Exercise Programme Form</title>
		<?php
			loadJavascript("../scripts/loader.js");
			loadJQuery();
		?>
		<script>
			loadCSS("../css/styles.css");

			jQuery(function($){ //on document.ready
			});
		</script>
	</head>
	<body>
		<div id='dhead'>
			<h2><?php echo ($addrec ? "Add New" : "Edit") . " Strength Programme Record For " . $_SESSION['name']; ?></h2>
			<span class="required_notification">* Denotes Required Field</span>
		</div>
		<div id='fcon'>
			<form class="data_form" action="<?php echo htmlentities($_SERVER['PHP_SELF']).($addrec ? '' : '?id='.$_GET['id']); ?>" method="post">
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
					<label for="level">Weight</label>
					<input style="width:40px" type="text" name="weight" id="weight" value="<?php echo ($input['weight'] == null ? null : number_format($input['weight'], 0)); ?>" placeholder="0">
					<span class="<?php echo isset($error_array['weight']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['weight']) ? $error_array['weight'] : 'Please enter a valid weight'; ?></span>
				</p>
				<p class="tbox">
					<label for="minutes">Number of Sets</label>
					<input style="width:40px" type="text" name="sets" id="sets" value="<?php echo ($input['sets'] == null ? null : number_format($input['sets'], 0)); ?>" placeholder="0">
					<span class="<?php echo isset($error_array['sets']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['sets']) ? $error_array['sets'] : 'Please enter a valid number of sets'; ?></span>
				</p>
				<p class="tbox">
					<label for="speed">Number of Reps</label>
					<input style="width:40px" type="text" name="reps" id="reps" value="<?php echo ($input['reps'] == null ? null : number_format($input['reps'], 0)); ?>" placeholder="0">
					<span class="<?php echo isset($error_array['reps']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['reps']) ? $error_array['reps'] : 'Please enter a valid number of reps'; ?></span>
				</p>
				<p class="submit">
					<a href="display_strength_progs.php" class="button">Back To Results</a>
					<button type="submit" id="submit" class="button"><?php echo ($addrec ? 'Add' : 'Update'); ?> Record</button>

				</p>
			</form>
		</div>
	</body>
</html>