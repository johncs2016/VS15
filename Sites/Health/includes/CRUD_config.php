<?php
	ini_set('error_reporting', E_ALL | E_STRICT);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	
	//Variables for connecting to your database.
	//These variable values come from your hosting account.
	
	$config = array(
		'driver'	=>	"sqlsrv",
		'server'	=>	"(local)\sqlexpress",
		'username'	=>	"sa",
		'dbname'	=>	"Health",
		'password'	=>	"hibs2016"
	);

