<!doctype html>
<html lang="en">
	<head>
		<title>CMS Tutorial</title>

		<link rel="stylesheet" href="assets/css/style.css" />
	</head>

	<body>
		<div class="container">
			<a href="index.php" id="logo">CMS Tutorial</a>

			&nbsp; &nbsp;

			<small>
				<a href="admin">admin &rarr;</a> &nbsp; &nbsp;
				<a href="admin/edit.php?id=<?php echo $id; ?>">edit</a> &nbsp; &nbsp;
				<a href="admin/delete.php?id=<?php echo $id; ?>">delete</a>
			</small>

			<br />

			<article>
				<h3><?php echo $title; ?></h3>
				<p class="meta">Posted <?php echo $timestamp; ?>.</p>

				<div class="content">
					<?php echo $content; ?>
				</div>

				<br />

				<small class="links">
					<?php if ($id != 1) : ?>

						<a href="article.php?id=<?php echo $id - 1; ?>">Previous Article</a>

						&nbsp; &nbsp;

					<?php endif; ?>

					<?php if (! $is_last_article) : ?>

						<a href="article.php?id=<?php echo $id + 1; ?>">Next Article</a>

					<?php endif; ?>
				</small>
			</article>

		</div>
	</body>
</html>