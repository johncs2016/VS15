<!doctype html>
<html lang="en">
	<head>
		<title>CMS Tutorial</title>

		<link rel="stylesheet" href="assets/css/style.css" />
	</head>

	<body>
		<div class="container">
			<a href="index.php" id="logo">CMS Tutorial</a> &nbsp; &nbsp; <small><a href="admin">admin &rarr;</a></small><br />

			<?php if (!empty($articles)) : ?>

				<ol>
					<?php foreach ($articles as $article) : ?>

						<li><a href="article.php?id=<?php echo $article->article_id; ?>"><?php echo $article->article_title; ?></a></li>

					<?php endforeach; ?>
				</ol>

			<?php else : ?>

				No articles have been posted yet.

			<?php endif; ?>

		</div>
	</body>
</html>