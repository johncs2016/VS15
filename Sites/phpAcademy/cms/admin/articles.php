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
	$articles = $article->fetch_all();

	if (isset($_POST['title'], $_POST['content'])) {
		$title = $_POST['title'];
		$content = $_POST['content'];

		if (empty($title) or empty($content)) $error = 'All fields are required!';

		if (!isset($error)) {
			$article->add($title, $content);

			header('Location: articles.php');
			exit('Article added successfully.');
		}
	}

	include_once 'templates/articles.php';
}
?>