<?php
include_once 'includes/bootstrap.php';
include_once 'includes/classes/Article.php';

$article = new Article;
$articles = $article->fetch_all();

include 'templates/index.php';
?>