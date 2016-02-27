<?php
	require_once("BMconfig.php");

	//Connecting to your database
	$dsn = 'mysql:host='.$config['db']['hostname'].';dbname='.$config['db']['dbname'];
	$db = new PDO($dsn, $config['db']['username'], $config['db']['password']);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
