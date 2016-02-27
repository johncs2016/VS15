<!doctype html>
<html lang="en">
	<head>
		<title>CMS Tutorial</title>

		<link rel="stylesheet" href="../assets/css/style.css" />
	</head>

	<body>
		<div class="container">
			<a href="index.php" id="logo">CMS Tutorial</a>

			&nbsp; &nbsp; 

			<small>
				<a href="../index.php">&larr; blog</a> &nbsp; &nbsp; 
				<a href="users.php">users</a> &nbsp; &nbsp; 
				<a href="articles.php">articles</a> &nbsp; &nbsp; 
				<a href="logout.php">logout (<?php echo $user->username(); ?>)</a>
			</small>

			<br /><br />

			<?php if (!empty($articles)) : ?>

				<?php foreach ($articles as $article) : ?>

					<?php echo $article->article_title; ?>&nbsp;

					<small>
						<a href="edit.php?id=<?php echo $article->article_id; ?>">edit</a> &nbsp;
						<a href="delete.php?id=<?php echo $article->article_id; ?>">delete</a>
					</small>

					<?php if (end($articles) != $article) : ?><br /> <?php endif; ?>

				<?php endforeach; ?>

			<?php else : ?>

				<p>There are no articles yet.</p>

			<?php endif; ?>

			<br /><br />

			<h3>Add Article</h3>

			<?php if (isset($error)) echo '<b><font color="#aa0000">' . $error . '</font></b><br /><br />'; ?>

			<form action="articles.php" method="post">
				<input type="text" name="title" placeholder="Title" /><br />
				<textarea cols="40" rows="10" name="content">Content</textarea><br />
				<input type="submit" value="Add Article" />
			</form>

		</div>
	</body>
</html>