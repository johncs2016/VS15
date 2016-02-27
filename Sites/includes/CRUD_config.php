<?php
	// Set Appropriate Error Reporting Settings
	ini_set('error_reporting', E_ALL | E_STRICT);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	
	//Variables for connecting to your database.
	//These variable values come from your hosting account.
	
	$config = array(
		'driver'	=>	"mysql",
		'hostname'	=>	"127.0.0.1",
		'username'	=>	"health",
		'dbname'	=>	"health",
		'password'	=>	"Eagle@2013"
	);
?>
