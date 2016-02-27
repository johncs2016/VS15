<?php
include_once '../includes/bootstrap.php';
include_once '../includes/classes/User.php';

$user = new User;

if (!isset($_SESSION['username'])) {
	if (isset($_POST['username'], $_POST['password'])) {
		$username = $_POST['username'];
		$password = md5($_POST['password']);

		if (empty($username) or empty($password)) {
			$error = 'All fields are required!';
		} else if (!$user->check_login($username, $password)) {
			$error = 'The credentials you provided are invalid!';
		}

		if (!isset($error)) {
			$_SESSION['username'] = $username;

			header('Location: index.php?m=1');
			exit('Congratulations, ' . $username . '! You have been successfully logged in!');
		}
	}

	include_once 'templates/login.php';
} else {
	include_once 'templates/index.php';
}
?>