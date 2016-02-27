<?php	
	session_start();
	ini_set('session.gc_maxlifetime', 1800);

	require_once("user.php");
	require_once("details.php");
	$loggedin = false;
	$sql = "SELECT * FROM tblusers WHERE sessionid = \"" . session_id() . "\" LIMIT 1";

	$db = tblusers::getConnection();

	$query = $db->prepare($sql);

	$query->execute();

	$row = $query->fetch(PDO::FETCH_ASSOC);

	define("LOGIN", "login.php");
	$port = $_SERVER['SERVER_PORT'];
	$server = "johncs2015.asuscomm.com";
	$protocol = "https";
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
			header("Location: " . $protocol . "://" . $server . dirname($req) . "/" . LOGIN);
			exit;
		}
	}
?>
