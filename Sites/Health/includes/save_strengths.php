<?php
	include_once("sessions.php");
	include "strengthprogs.php";
	include "strengthequipment.php";
	
	$progs = tblstrengthprogs::getAll();
	$now = (new DateTime(date("Y-m-d")))->format('Y-m-d');
	echo '<p>' . $_SESSION['name'] . '</p>';
	foreach ($progs as $prog) {
		$equip = tblstrengthequipment::get($prog->getEquipmentID());
		echo '<p>' . $equip->getName() . '</p>';
	}
?>
