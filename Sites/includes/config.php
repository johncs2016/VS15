<?php
	ini_set('error_reporting', E_ALL);
	//Variables for connecting to your database.
	//These variable values come from your hosting account.
	$config['db'] = array(
		'hostname'	=> "localhost",
		'username'	=> "health",
		'dbname'	=> "health_records",
		'password'	=> "JpBMqjHGfyRBf2zc",
		'usertable'	=>	"tblweeklydata",
		'units'		=>	Array("Y" => "kg","Z" => "cm"),
		'descrip'	=>	Array("Y" => "Weight", "Z" => "Waist Size"),
		'canvasID'	=>	Array("Y" => "cvsWeight", "Z" => "cvsWaist"),
		'Fields'	=>	Array(	"X" =>	"ObservationDate",
								"Y"	=>	"Weight",
								"Z" =>	"WaistSize")
		);
?>
