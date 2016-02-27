<?php
include_once '../includes/bootstrap.php';
include_once '../includes/classes/User.php';
include_once '../includes/classes/Article.php';

$user = new User;
$article = new Article;

if (!isset($_SESSION['username'])) {
	header('Location: index.php');
	exit('You must be logged in to do that.');
} else {
	if (isset($_GET['id'])) {
		$id = $_GET['id'];

		$article->delete($id);

		header('Location: articles.php');
		exit('Article deleted successfully.');
	} else {
		header('Location: articles.php');
		exit('You didn\'t specify an article!');
	}
}
?>