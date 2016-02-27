<?php
	include 'gender.php';
	include 'twindex.php';
	include 'user.php';
	
	$genders = tblgenders::getAll();
	$twindexes = tbltwindex::getAll();
	$addrec = (!isset($_GET['id']));
	$object = ($addrec ? null : tblusers::get($_GET['id']));
	$input = array(
		'first'			=>	($addrec ? null : $object->getFirstName()),
		'last'			=>	($addrec ? null : $object->getLastName()),
		'email'			=>	($addrec ? null : $object->getEmailAddress()),
		'gender'		=>	($addrec ? 1 : $object->getGenderid()),
		'dob'			=>	($addrec ? new DateTime() : $object->getDOB()),
		'height'		=>	($addrec ? 0 : $object->getHeight()),
		'step'			=>	($addrec ? 0 : $object->getStepSize()),
		'wrist'			=>	($addrec ? 0 : $object->getWristSize()),
		'forearm'		=>	($addrec ? 0 : $object->getForearmSize()),
		'hip'			=>	($addrec ? 0 : $object->getHipSize()),
		'isimperial'	=>	($addrec ? 1 : $object->getIsImperial()),
		'twindex'		=>	($addrec ? 1 : $object->getTWIndexid())
	);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Step Counts Data Entry Form</title>
		<link rel="stylesheet" type="text/css" href="../css/website.css">
		<script type="text/javascript">
    		var datefield=document.createElement("input")
    		datefield.setAttribute("type", "date")
    		if (datefield.type!="date"){ //if browser doesn't support input type="date", load files for jQuery UI Date Picker
        		document.write('<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />\n')
        		document.write('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"><\/script>\n')
        		document.write('<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"><\/script>\n')
    		}
		</script>
 
		<script>
		if (datefield.type!="date"){ //if browser doesn't support input type="date", initialize date picker widget:
    		jQuery(function($){ //on document.ready
        		$('#dob').datepicker();
    		})
		}
		</script>
	</head>
	<body>
		<h2>Add New User</h2>
		<form action="process_user.php" method="post">
			<table border="0">
				<tr>
					<td>First Name</td><td><input type="text" name="first" value = "<?php echo $input['first']; ?>"></td>
					<td>Last Name</td><td><input type="text" name="last" value = "<?php echo $input['last']; ?>"></td>
				</tr>
				<tr>
					<td>Gender:</td><td colspan="3"><select name="gender">
						<?php
							foreach ($genders as $object) {
								$id = $object->getID();
								echo '<option ' . (($id == $input['gender']) ? 'selected ' : '') . 'value="'.$id.'">' . $object->getGender() . '</option>';
							}
						?>
					</select></td>
				</tr>
				<tr>
					<td>Email Address</td><td colspan="3"><input type="text" name="email" value = "<?php echo $input['email']; ?>"></td>
				</tr>
				<tr>
					<td>Date Of Birth:</td><td colspan="3"><input type="date" id="dob" name="dob" value="<?php echo $input['dob']; ?>"></td>
				</tr>
				<tr>
					<td>Height:</td><td colspan="3"><input type="text" name="height" value = "<?php echo $input['height']; ?>"></td>
				</tr>
				<tr>
					<td>Step Size</td><td><input type="text" name="step" value = "<?php echo $input['step']; ?>"></td>
					<td>Wrist Size</td><td><input type="text" name="wrist" value = "<?php echo $input['wrist']; ?>"></td>
				</tr>
				<tr>
					<td>Forearm Size</td><td><input type="text" name="forearm" value = "<?php echo $input['forearm']; ?>"></td>
					<td>Hip Size</td><td><input type="text" name="hip" value = "<?php echo $input['hip']; ?>"></td>
				</tr>
				<tr>
					<td colspan="4"><input type="checkbox" name="isimperial" value="<?php echo $input['isimperial']; ?>"<?php echo $input['isimperial'] ? ' checked="checked"' : ''; ?>>Use Imperial Units</td>
				</tr>
				<tr>
					<td colspan="2">Reference for Target Weight:</td><td colspan="2"><select name="twindex">
						<?php
							foreach ($twindexes as $object) {
								$id = $object->getID();
								echo '<option ' . (($id == $input['twindex']) ? 'selected ' : '') . 'value="'.$id.'">' . $object->getRefPoint() . '</option>';
							}
						?>
					</select></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" value="<?php echo ($addrec ? 'Add' : 'Update'); ?> Record"></td>
				</tr>
			</table>
		</form>
	</body>
</html>
