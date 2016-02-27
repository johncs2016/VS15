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
	$prevrec = ($addrec ? tblweeklydata::getInitialCurrent() : tblweeklydata::getPreviousNext($id, true));
	$prevweight = ($addrec ? tblweeklydata::getFirstLastValue('weight', false, $obsdate, true) : tblweeklydata::getPrevNextValue('weight', $obsdate, true));
	$prevwaist = ($addrec ? tblweeklydata::getFirstLastValue('waistsize', false, $obsdate, true) : tblweeklydata::getPrevNextValue('waistsize', $obsdate, true));
	$now = new DateTime();
	if (!empty($_POST)) {
		$input = array(
			'odate'		=>	DateTime::createFromFormat('d/m/Y', $_POST['odate']),
			'weight'	=>	$_POST['weight'],
			'waist'		=>	$_POST['waist']
		);
	}
	else {
		$object = ($addrec ? null : $curr);
		$input = array(
			'odate'		=>	($addrec ? $now : $object->getObsDate()),
			'weight'	=>	($addrec ? null : $object->getWeight()),
			'waist'		=>	($addrec ? null : $object->getWaistSize())
		);
	}

	$weight_change = array(
		'label'	=>	($input['weight'] == null) ? "" : "Weight Change:",
		'value'	=>	($input['weight'] == null) ? "" : display::display_change(display::getLatestDifference('weight', $id), false, $isimp)
	);

	$waist_change = array(
		'label'	=>	($input['waist'] == null) ? "" : "Waist Size Change:",
		'value'	=>	($input['waist'] == null) ? "" : display::display_change(display::getLatestDifference('waistsize', $id), true, $isimp)
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
			$valid = $val_object->validate_date("obsdate", $properties["_obsdate"]);
			$valid = ($valid && $val_object->validate_float("weight", $properties["_weight"]));
			$valid = ($valid && $val_object->validate_float("waistsize", $properties["_waistsize"]));
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
				var change = (val2 == 0 ? "" : display_change(val1, val2, (flag ? 0 : 3), isimp));
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
		<div id='fcon'>
			<form class="data_form" action="<?php echo htmlentities($_SERVER['PHP_SELF']).($addrec ? '' : '?id='.$_GET['id']); ?>" method="post">
				<p class="icheck">
					<input type="checkbox" id="isimp" name="isimp" value="Yes" onclick="toggle_fields()"<?php echo ($isimp ? " checked" : ""); ?>>Imperial Units used
				</p>
				<p class="dpicker">
					<label for="odate">Observation Date</label>
					<input type="text" id="odate" name="odate" value="<?php echo ($input['odate'] == null ? '' : $input['odate']->format('d/m/Y')); ?>" placeholder="<?php echo $now->format('d/m/Y'); ?>">
					<span class="<?php echo isset($error_array['obsdate']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['obsdate']) ? $error_array['obsdate'] : '* Please select a date'; ?></span>
				</p>
				<p class="tbox">
					<label for="weight">Weight</label>
					<input style="width:40px" type="text" name="weight" id="weight" class="noimp" value="<?php echo ($input['weight'] == null ? null : number_format($input['weight'], 1)); ?>" placeholder="0.0" onchange="update_stlb()">
					<span id="kgspan" class="noimp">kg. &nbsp;</span>
					<input style="width:25px" type="text" name="stone" id="stone" class="isimp" onchange="update_weight()" value="<?php echo ($input['weight'] == null ? "0" : conversions::intSTONEfromKG($input['weight'])) ?>" placeholder="0">
					<span id="stspan" class="isimp">st. &nbsp;</span>
					<select style="width:40px" name="pounds" id="pounds" class="isimp" onchange="update_weight()">
					<?php
						$lb = conversions::intPOUNDfromKG($input['weight']);
						for($i = 0; $i < 14; $i++) {
							$html = "<option value=$i";
							if($i == $lb) $html .= " selected";
							$html .= ">$i</option>";
							echo $html;
						}
					?>
					</select>
					<span id="lbspan" class="isimp">lb. &nbsp;</span>
					<span class="<?php echo isset($error_array['weight']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['weight']) ? $error_array['weight'] : 'Please enter a valid weight'; ?></span>
				</p>
				<div class="dcon">
					<p id="weightlabel" class="tlabel"><?php echo $weight_change['label']; ?></p>
					<p id="weightdata" class="tdata"> <?php echo $weight_change['value']; ?></p>
				</div>
				<p class="tbox">
					<label for="waist">Waist Size</label>
					<input style="width:40px" type="text" name="waist" id="waist" class="noimp" placeholder="0.0" value="<?php echo ($input['waist'] == null ? null : number_format($input['waist'], 1)); ?>" onchange="update_inches()">
					<span id="cmspan" class="noimp">cm. &nbsp;</span>
					<input style="width:40px" type="text" name="inches" id="inches" class="isimp" placeholder="0.0" value="<?php echo ($input['waist'] == null ? null : number_format(conversions::INfromCM($input['waist']), 1)); ?>" onchange="update_waist()">
					<span id="inspan" class="isimp">in. &nbsp;</span>
					<span class="<?php echo isset($error_array['waistsize']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['waistsize']) ? $error_array['waistsize'] : 'Please enter a valid waist size'; ?></span>
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
