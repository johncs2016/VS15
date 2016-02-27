<?php
	include_once "sessions.php";
	require_once("load_libraries.php");
	require_once("weeklydata.php");
	require_once("validator.php");
	require_once("display_functions.php");
	require_once("composition.php");
	require_once("conversion.php");
	
	$id = (isset($_GET['id']) ? $_GET['id'] : false);
	$userid = $_SESSION['userid'];
	$user = tblusers::get($userid);
	$isimp = $user->getIsImperial();
	$addrec = !isset($_GET['id']);
	$curr = ($addrec ? tblweeklydata::getInitialCurrent() : tblweeklydata::get($id));
	$obsdate = $curr->getObsDate();
	$firstrec = tblweeklydata::getInitialCurrent(true);
	$firstid = $firstrec->getID();
	$currid = $curr->getID();
	$prevrec = ($addrec ? tblweeklydata::getInitialCurrent() : tblweeklydata::getPreviousNext($id, true));
	$prevweight = $prevrec->getWeight();
	$prevwaist = $prevrec->getWaistSize();
	$now = new DateTime();
	if (!empty($_POST)) {
		$input = array(
			'odate'		=>	DateTime::createFromFormat('d/m/Y', $_POST['odate']),
			'weight'	=>	$_POST['weight'],
			'waist'		=>	$_POST['waist']
		);
	}
	else {
		$object = $curr;
		$input = array(
			'odate'		=>	($addrec ? $now : $object->getObsDate()),
			'weight'	=>	$object->getWeight(),
			'waist'		=>	$object->getWaistSize()
		);
	}
	
	$st = conversions::intSTONEfromKG($input['weight']);
	$lb = round(conversions::intPOUNDfromKG($input['weight']), 1);
	if ($lb == 14) {
		$st++;
		$lb = 0;
	}

	$weight_change = array(
		'label'	=>	($input['weight'] == null) ? "" : ($currid == $firstid ? '' : "Weight Change:"),
		'value'	=>	($input['weight'] == null) ? "" : ($currid == $firstid ? '' : display::display_change(display::getLatestDifference('weight', $id), false, $isimp))
	);

	$waist_change = array(
		'label'	=>	($input['waist'] == null) ? "" : ($currid == $firstid ? '' : "Waist Size Change:"),
		'value'	=>	($input['waist'] == null) ? "" : ($currid == $firstid ? '' : display::display_change(display::getLatestDifference('waistsize', $id), true, $isimp))
	);
	
	if (!empty($_POST)) {

		$action = (isset($_GET['id']) ? 'updated' : 'added');

		try {
			$properties = array(
				'_userid'		=>	$userid,
				'_obsdate'		=>	$_POST['odate'],
				'_weight'		=>	$_POST['weight'],
				'_waistsize'	=>	$_POST['waist']
			);

			if (empty($properties['_weight'])) {
				$properties['_weight'] = 0;
			}

			if (empty($properties['_waistsize'])) {
				$properties['_waistsize'] = 0;
			}

			$val_object = new validator();
			$valid = $val_object->validate_date("obsdate", $properties["_obsdate"], true);
			$valid = ($valid && $val_object->validate_float("weight", $properties["_weight"], false, 0));
			$valid = ($valid && $val_object->validate_float("waistsize", $properties["_waistsize"], false, 0));
			$valid = ($valid && $val_object->validate_integer("Stones", $_POST["stone"], false, 0));
			$valid = ($valid && $val_object->validate_float("Pounds", $_POST["pounds"], false, 0));
			$valid = ($valid && $val_object->validate_float("Inches", $_POST["inches"], 0));
			$error_array = $val_object->getAllErrors();
			
			if (empty($error_array)) {
				if(isset($_GET['id'])) $properties['_id'] = $_GET['id'];
				if($action == 'added') {
					$object = tblweeklydata::create($properties);
				} else {
					$object = tblweeklydata::get($_GET['id']);
					$object->update($properties);
				}
				$action = 'record has been ' . $action . '.';
				header("Location: display_data.php?action=" . $action);
				exit();
			}
		}
		catch (PDOException $e) {
			$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
			header("Location: display_data.php?action=" . $action);
			exit();
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<title>Data Entry Form</title>
		<?php
			loadJQuery();
			loadJavascript("../scripts/loader.js");
		?>
		<script>
			loadCSS("../css/styles.css");
			loadCSS("../css/list.css");
			require("../scripts/dataForm.js");

			function dispChange(flag) {
				var field = (flag ? "weight" : "waist");
				var labfield = field + "label";
				var labvalue = (flag ? "Weight" : "Waist Size") + " Change";
				var valfield = field + "data";
				var val1 = (flag ? <?php echo $prevweight; ?> : <?php echo $prevwaist; ?>);
				var val2 = document.getElementById(field).value;
				val2 = (val2 == "" ? 0 : Number(val2));
				var isimp = $('#isimp').is(':checked');
				var change = (val2 == 0 ? "" : display_change(val1, val2, (flag ? 0 : 3), isimp, true));
				document.getElementById(labfield).innerHTML = (val2 == 0 ? "" : labvalue);
				document.getElementById(valfield).innerHTML = change;
			}
		</script>
	</head>
	<body>
		<div id='dhead'>
			<h2><?php echo ($addrec ? "Add New" : "Edit") . " Weekly Data Record For " . $_SESSION['name']; ?></h2>
			<span class="required_notification">* Denotes Required Field</span>
		</div>
		<div id="error_container">
			<p>Please correct the following errors and try again:-</p>
			<ul></ul>
		</div>
		<div id='fcon'>
			<form class="data_form" id="data_form" action="<?php echo htmlentities($_SERVER['PHP_SELF']).($addrec ? '' : '?id='.$_GET['id']); ?>" method="post">
				<p class="icheck">
					<input type="checkbox" id="isimp" name="isimp" value="Yes" onclick="toggle_fields()"<?php echo ($isimp ? " checked" : ""); ?>>Imperial Units used
				</p>
				<p class="dpicker">
					<label for="odate">Observation Date</label>
					<input type="text" id="odate" name="odate" value="<?php echo ($input['odate'] == null ? '' : $input['odate']->format('d/m/Y')); ?>" placeholder="<?php echo $now->format('d/m/Y'); ?>">
					<span id="date_error" class="<?php echo isset($error_array['obsdate']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['obsdate']) ? $error_array['obsdate'] : '* Please select a date'; ?></span>
				</p>
				<p class="tbox">
					<label for="weight">Weight</label>
					<input style="width:60px" type="number" min="0" step="0.1" name="weight" id="weight" class="noimp" value="<?php echo number_format($input['weight'], 1); ?>" placeholder="0.0" onchange="update_stlb()">
					<span id="kgspan" class="noimp">kg. &nbsp;</span>
					<label for="stone" class="nodisplay">Stone</label>
					<input style="width:40px" type="number" min="0" name="stone" id="stone" class="isimp" onchange="update_weight()" value="<?php echo $st; ?>" placeholder="0">
					<span id="stspan" class="isimp">st. &nbsp;</span>
					<label for="pounds" class="nodisplay">Pounds</label>
					<input style="width:60px" type="number" min="0" max="13.9" step="0.1" name="pounds" id="pounds" class="isimp" onchange="update_weight()" value="<?php echo number_format($lb, 1) ?>" placeholder="0.0">
					<span id="lbspan" class="isimp">lb. &nbsp;</span>
					<span id="weight_error" class="<?php echo isset($error_array['weight']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['weight']) ? $error_array['weight'] : 'Please enter a valid number of kilos'; ?></span>
					<span id="stones_error" class="<?php echo isset($error_array['Stones']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['Stones']) ? $error_array['Stones'] : 'Please enter a valid number of stones'; ?></span>
					<span id="pounds_error" class="<?php echo isset($error_array['Pounds']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['Pounds']) ? $error_array['Pounds'] : 'Please enter a valid number of pounds'; ?></span>
				</p>
				<div class="dcon">
					<p id="weightlabel" class="tlabel"><?php echo $weight_change['label']; ?></p>
					<p id="weightdata" class="tdata"> <?php echo $weight_change['value']; ?></p>
				</div>
				<p class="tbox">
					<label for="waist">Waist Size</label>
					<input style="width:60px" type="number" min="0" step="0.1" name="waist" id="waist" class="noimp" placeholder="0.0" value="<?php echo number_format($input['waist'], 1); ?>" onchange="update_inches()">
					<span id="cmspan" class="noimp">cm. &nbsp;</span>
					<label for="inches" class="nodisplay">Inches</label>
					<input style="width:60px" type="number" min="0" step="0.1" name="inches" id="inches" class="isimp" placeholder="0.0" value="<?php echo number_format(conversions::INfromCM($input['waist']), 1); ?>" onchange="update_waist()">
					<span id="inspan" class="isimp">in. &nbsp;</span>
					<span id="waist_error" class="<?php echo isset($error_array['waistsize']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['waistsize']) ? $error_array['waistsize'] : 'Please enter a valid number of centimeters'; ?></span>
					<span id="inches_error" class="<?php echo isset($error_array['Inches']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['Inches']) ? $error_array['Inches'] : 'Please enter a valid number of inches'; ?></span>
				</p>
				<div class="dcon">
					<p id="waistlabel" class="tlabel"><?php echo $waist_change['label']; ?></p>
					<p id="waistdata" class="tdata"> <?php echo $waist_change['value']; ?></p>
				</div>
				<p class="submit">
					<a href="display_data.php" class="button">Back To Results</a>
					<button type="submit" id="submit" class="button"><?php echo ($addrec ? 'Add' : 'Update'); ?> Record</button>
			<a class="button" href="logout.php">Log Out</a>
				</p>
			</form>
		</div>
	</body>
</html>
