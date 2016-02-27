<?php
	session_start();
	ini_set('session.gc_maxlifetime', 1800);

	require_once("user.php");
	require_once("details.php");
	$loggedin = false;
	$sql = tblusers::getSelect(1) . " WHERE sessionid = ?";

	$db = tblusers::getConnection();
    
	$query = $db->prepare($sql);

	$query->execute(array(session_id()));
        
	$row = $query->fetch(PDO::FETCH_ASSOC);

	define("LOGIN", "login.php");
	$server = strtolower($_SERVER['HTTP_HOST']);
	$protocol = "https://";
	$pageURL = $protocol . $server . $_SERVER['REQUEST_URI'];

	if(!empty($row)) {
		$_SESSION['userid'] = $row["id"];
		$_SESSION['username'] = $row["username"];
		$details = tbldetails::get($row["detailsid"]);
		$_SESSION['name'] = $details->fullname();
		$_SESSION['first'] = $details->getFirstName();
		$loggedin = true;
	} else {
		$req = $_SERVER['PHP_SELF'];
		if(basename($req) != LOGIN) {
			header("Location: " . $protocol . $server . dirname($req) . "/includes/" . LOGIN);
			exit;
		}
	}
?>
