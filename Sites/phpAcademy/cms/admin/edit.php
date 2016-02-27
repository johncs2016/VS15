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

		$query = $pdo->prepare('SELECT * FROM articles WHERE article_id = ?');
		$query->execute(array($id));
		$root = $query->fetch(PDO::FETCH_OBJ);

		if (empty($root)) {
			header('Location: articles.php');
			exit('The article you specified doesn\'t exist!');
		}

		$title = $root->article_title;
		$content = $root->article_content;

		if (isset($_POST['title'], $_POST['content'])) {
			$title = $_POST['title'];
			$content = $_POST['content'];

			if (empty($title) or empty($content)) $error = 'All fields are required!';

			if (!isset($error)) {
				$article->update($id, $title, $content);

				header('Location: articles.php');
				exit('Article has been updated successfully.');
			}
		}

		include_once 'templates/edit.php';
	} else {
		header('Location: articles.php');
		exit('You didn\'t specify an article!');
	}
}
?>