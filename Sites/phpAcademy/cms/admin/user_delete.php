<?php
include_once '../includes/bootstrap.php';
include_once '../includes/classes/User.php';

$user = new User;

if (!isset($_SESSION['username'])) {
	header('Location: index.php');
	exit('You must be logged in to do that.');
} else {
	if (isset($_GET['id'])) {
		$id = $_GET['id'];

		$user->delete($id);

		header('Location: users.php');
		exit('User has been successfully deleted.');
	} else {
		header('Location: users.php');
		exit('No user specified.');
	}
}
?>