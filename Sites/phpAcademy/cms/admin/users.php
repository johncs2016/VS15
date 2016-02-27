<?php
include_once '../includes/bootstrap.php';
include_once '../includes/classes/User.php';

$user = new User;

if (!isset($_SESSION['username'])) {
	header('Location: index.php');
	exit('You must be logged in to do that.');
} else {
	$users = $user->fetch_all();

	if (isset($_POST['username'], $_POST['password'])) {
		$username = $_POST['username'];
		$password = md5($_POST['password']);

		if (empty($username) or empty($password)) $error = 'All fields are required!';
		if ($user->user_exists($username)) $error = 'That user already exists!';

		if (!isset($error)) {
			$user->add($username, $password);

			header('Location: users.php');
			exit('User has been successfully added.');
		}
	}

	include_once 'templates/users.php';
}
?>