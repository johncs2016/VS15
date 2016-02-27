<?php
	include_once("sessions.php");

	$errors = array();
	$success = array();

	if(isset($_POST['loginsubmit']) && $_POST['loginsubmit'] == "true") {

		$loginuser = trim($_POST['username']);
		$loginpassword = trim($_POST['password']);

		if(!preg_match("/([a-zA-Z0-9]+){3,16}/", $loginuser)) {
			$errors['loginuser'] = "Your username is invalid";
		}

		if(!preg_match("/([a-zA-Z0-9]+){6,12}/", $loginpassword)) {
			$errors['loginpassword'] = "Your password is invalid";
		}
		
		if(!$errors) {
			$sql = "SELECT * FROM tblusers WHERE username = \"" . $loginuser . "\" AND password = \"" . md5($loginpassword) . "\" LIMIT 1";
			$db = tblusers::getConnection();
			$query = $db->prepare($sql);
			$query->execute();
			$row = $query->fetch(PDO::FETCH_ASSOC);
			if(!empty($row)) {
				$user = tblusers::get($row['id']);
				$user->updateProperty("_sessionid", session_id());

				header("Location: dashboard.php");
				exit;
			} else {
				$errors['login'] = "No user was found with the details provided";
			}
		}
	}

	if(isset($_POST['registersubmit']) && $_POST['registersubmit'] == "true") {
		$registeruser = trim($_POST['username']);
		$registerpassword = trim($_POST['password']);
		$registerconfirmpassword = trim($_POST['password']);

		if(!preg_match("/[a-zA-Z0-9]{3,16}/", $registeruser)) {
			$errors['registeruser'] = "Your username is invalid";
		}

		if(!preg_match("/[a-zA-Z0-9]{6,12}/", $registerpassword)) {
			$errors['registerpassword'] = "Your password is invalid";
		}

		if($registerpassword != $registerconfirmpassword) {
			$errors['registerconfirmpassword'] = "Your passwords do not match";
		}
		
		$sql = "SELECT * FROM tblusers WHERE username = \"" . $registeruser . "\" LIMIT 1";
		$db = tblusers::getConnection();
		$query = $db->prepare($sql);
		$query->execute();
		$row = $query->fetch(PDO::FETCH_ASSOC);
		if(!empty($row)) {
			$errors['registeruser'] = "That username has already been taken";
		}

		if(!$errors) {
			$now = date('Y-m-d');
			$userarray = array(
				'username'	=>	$registeruser,
				'password'	=>	md5($registerpassword),
				'dateregistered'	=>	$now,
			);
			$user = tblusers::create($userarray);
			if($user != null) {
				$success['register'] = "Thank you for registering, you may now log in";
			} else {
				$errors['register'] = "There was an problem with registering you.";
			}
		}
	}
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Log Into Health Records Application</title>
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;"/>
		<link rel="stylesheet" type="text/css" href="../css/default.css"/>
	</head>
	<body>
		<header><h1>Login / Register Here</h1></head>
		<form class="box400" name="loginform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<h2>Login</h2>
			<?php if(isset($errors['login'])) print "<div class=\"invalid\">" . $errors['login'] . "</div>"; ?>
			<label for="username">User Name</label>
			<input type="text" name="username" value="<?php echo (isset($loginuser) ? htmlspecialchars($loginuser) : ''); ?>" />
			<?php if(isset($errors['loginuser'])) print "<div class=\"invalid\">" . $errors['loginuser'] . "</div>"; ?>
			<label for="password">Password <span class="info">6-12 chars</span></label>
			<input type="password" name="password" value="" />
			<?php if(isset($errors['loginpassword'])) print "<div class=\"invalid\">" . $errors['loginpassword'] . "</div>"; ?>
			<label for="loginsubmit">&nbsp;</label>
			<input type="hidden" name="loginsubmit" id="loginsubmit" value="true" />
			<input type="submit" value="Login" />
		</form>
		<form class="box400" name="registerform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<h2>Register</h2>
			<?php if(isset($success['register'])) print "<div class=\"valid\">" . $success['register'] . "</div>"; ?>
			<?php if(isset($errors['register'])) print "<div class=\"invalid\">" . $errors['register'] . "</div>"; ?>
			<label for="username">User Name</label>
			<input type="text" name="username" value="<?php echo (isset($registeruser) ? htmlspecialchars($registeruser) : ''); ?>" />
			<?php if(isset($errors['registeruser'])) print "<div class=\"invalid\">" . $errors['registeruser'] . "</div>"; ?>
			<label for="password">Password</label>
			<input type="password" name="password" value="" />
			<?php if(isset($errors['registerpassword'])) print "<div class=\"invalid\">" . $errors['registerpassword'] . "</div>"; ?>
			<label for="confirmpassword">Confirm Password</label>
			<input type="password" name="confirmpassword" value="" />
			<?php if(isset($errors['registerpassword'])) print "<div class=\"invalid\">" . $errors['registerpassword'] . "</div>"; ?>
			<label for="registersubmit">&nbsp;</label>
			<input type="hidden" name="registersubmit" id="registersubmit" value="true" />
			<input type="submit" value="Register" />
		</form>
	<body>
</html>
