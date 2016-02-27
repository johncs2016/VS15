<?php
	ini_set('error_reporting', E_ALL);
	//Variables for connecting to your database.
	//These variable values come from your hosting account.
	$config['db'] = array(
		'hostname'	=> 	"localhost",
		'username'	=> 	"health",
		'dbname'	=> 	"health_records",
		'password'	=> 	"hibs2012",
		'usertable'	=>	"tblweeklydata",
		'units'		=>	"cm",
		'descrip'	=>	"Waist Size",
		'canvasID'	=>	"cvsWaist",
		'Fields'	=>	Array(	"X" => "ObservationDate",
								"Y"	=>	"WaistSize"
						)
		);
?>