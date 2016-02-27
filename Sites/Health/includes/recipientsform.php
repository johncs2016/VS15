<?php
	include_once("sessions.php");
	include("gender.php");
	include("recipient.php");
	include("rectypes.php");
	include("validator.php");
	require_once("load_libraries.php");
	
	$user = tblusers::get($_SESSION['userid']);
	$userdets = tbldetails::get($user->getDetailsID());
	$rectypes = tblrectypes::getAll();
	$genders = tblgenders::getAll();
	$addrec = (!isset($_GET['id']));
	$now = new DateTime();
	if (!empty($_POST)) {
		$input = array(
			'type'		=>	$_POST['type'],
			'first'		=>	$_POST['first'],
			'last'		=>	$_POST['last'],
			'gender'	=>	$_POST['gender'],
			'email'		=>	$_POST['email'],
			'dob'		=>	$_POST['dob'],
		);
	}
	else {
		$object = ($addrec ? null : tblreportrecipients::get($_GET['id']));
		if(!$addrec) {
			$details = ($addrec ? null : tbldetails::get($object->getDetailsID()));
		}
		$input = array(
			'type'		=>	($addrec ? 1 : $object->getRecID()),
			'first'		=>	($addrec ? null : $details->getFirstName()),
			'last'		=>	($addrec ? null : $details->getLastName()),
			'gender'	=>	($addrec ? 3 : $details->getGenderID()),
			'email'		=>	($addrec ? null : $details->getEmailAddress()),
			'dob'		=>	($addrec ? null : $details->getDOB()),
		);
	}
	if (!empty($_POST)) {

		$action = (isset($_GET['id']) ? 'updated' : 'added');

		try {
			$recprops = array(
				'_recid'	=>	$_POST['type'],
				'_userid'	=>	$user->getID()
			);
			$detprops = array(
				'_firstname'	=>	$_POST['first'],
				'_lastname'	=>	$_POST['last'],
				'_genderid'	=>	$_POST['gender'],
				'_emailaddress'	=>	$_POST['email'],
				'_dob'		=>	$_POST['dob']
			);

			$val_object = new validator();
			$valid = $val_object->validate_string("firstname", $detprops["_firstname"], true, 25);
			$valid = ($valid && $val_object->validate_string("lastname", $detprops["_lastname"], true, 25));
			$valid = ($valid && $val_object->validate_email("emailaddress", $detprops["_emailaddress"], 50));
			$valid = ($valid && $val_object->validate_date("dob", $detprops["_dob"]));
			$error_array = $val_object->getAllErrors();
			
			$gender = tblgenders::get($properties['_genderid']);
			if ($gender == null) {
				if (isset($error_array['genderid'])) $error_array['genderid'] .= "\n"; else $error_array['genderid'] = '';
				$error_array['genderid'] .= "Gender Does Not Exist";
				$valid = false;
			}
			
			if ($rectypes == null) {
				if (isset($error_array['recid'])) $error_array['recid'] .= "\n"; else $error_array['recid'] = '';
				$error_array['rectype'] .= "Email Type Does Not Exist";
				$valid = false;
			}
			
			if ($valid) {
				if(isset($_GET['id'])) $recprops['_id'] = $_GET['id'];
				if($action == 'added') {
					$details = tbldetails::create($detprops);
					$recprops['_detailsid'] = $details->getID();
					$object = tblreportrecipients::create($recprops);
				} else {
					$object = tblreportrecipients::get($_GET['id']);
					$details = tbldetails::get($object->getDetailsID());
					$recprops['_detailsid'] = $details->getID();
					$object->update($recprops);
					$details->update($detprops);
				}
				$action = 'record has been ' . $action . '.';
				header("Location: display_receipients.php?action=" . $action);
				exit();
			}
		}
		catch (PDOException $e) {
			$action = 'record cannot be ' . $action . '.<br>' . $e->getMessage();
			header("Location: display_receipients.php?action=" . $action);
			exit();
		}
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
		<title>Report Recipients Form</title>
		<?php
			loadJavascript("../scripts/loader.js");
			loadJQuery();
		?>
		<script>
			loadCSS("../css/styles.css");

			jQuery(function($){ //on document.ready
				$("#first").focus();
				$('input[type=date]').on('click', function(event) {
    					event.preventDefault();
				});
        		$('#dob').datepicker({ dateFormat: "dd/mm/yy", showButtonPanel: true }).val();
    		})
		</script>
	</head>
	<body>
		<div id='dhead'>
			<h2><?php echo ($addrec ? "Add New" : "Edit") . " Report Recipients Record For " . $_SESSION['name']; ?></h2>
			<span class="required_notification">* Denotes Required Field</span>
		</div>
		<div id='fcon'>
			<form class="data_form" action="<?php echo htmlentities($_SERVER['PHP_SELF']).($addrec ? '' : '?id='.$_GET['id']); ?>" method="post">
				<p class="tselect">
					<label for="type">To/Cc/Bcc</label>
					<select name="type" id="type">
						<?php
							foreach ($rectypes as $object) {
								$id = $object->getID();
								echo '<option ' . (($id == $input['type']) ? 'selected ' : '') . 'value="'.$id.'">' . $object->getType() . '</option>';
							}
						?>
					</select>
					<span class="form_hint">* Please Select a type from the drop-down list</span>
				</p>
				<p class="tbox">
					<label for="first">First Name</label>
					<input type="text" name="first" id="first" value = "<?php echo $input['first']; ?>" placeholder="Enter First Name">
					<span class="<?php echo isset($error_array['firstname']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['firstname']) ? $error_array['firstname'] : 'Please enter first name'; ?></span>
				</p>
				<p class="tbox">
					<label for="last">Last Name</label>
					<input type="text" name="last" id="last" value = "<?php echo $input['last']; ?>" placeholder="Enter Last Name">
					<span class="<?php echo isset($error_array['lastname']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['lastname']) ? $error_array['lastname'] : 'Please enter last name'; ?></span>
				</p>
				<p class="tselect">
					<label for="gender">Select Gender</label>
					<select name="gender" id="gender">
						<?php
							foreach ($genders as $object) {
								$id = $object->getID();
								echo '<option ' . (($id == $input['gender']) ? 'selected ' : '') . 'value="'.$id.'">' . $object->getGender() . '</option>';
							}
						?>
					</select>
					<span class="form_hint">* Please Select a gender from the drop-down list</span>
				</p>
				<p class="tbox">
					<label for="email">Email Address</label>
					<input type="text" name="email" id="email" value = "<?php echo $input['email']; ?>" placeholder="Enter Email Address">
					<span class="<?php echo isset($error_array['emailaddress']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['emailaddress']) ? $error_array['emailaddress'] : 'Please enter email address'; ?></span>
				</p>
				<p class="dpicker">
					<label for="dob">Date Of Birth</label>
					<input type="text" id="dob" name="dob" value="<?php echo ($input['dob'] == null ? '' : $input['dob']->format('d/m/Y')); ?>" placeholder="Please Enter Date Of Birth If Known">
					<span class="<?php echo isset($error_array['dob']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['dob']) ? $error_array['dob'] : '* Please select a date'; ?></span>
				</p>
				<p class="submit">
					<a href="display_recipients.php" class="button">Back To Results</a>
					<button type="submit" id="submit" class="button"><?php echo ($addrec ? 'Add' : 'Update'); ?> Record</button>
			<a class="button" href="logout.php">Log Out</a>

				</p>
			</form>
		</div>
	</body>
</html>
