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

			<?php if (isset($_GET['m']) and $_GET['m'] == 1) : ?>

				<strong style="color: green;">
					Congratulations, <?php echo $user->username(); ?>! You have been successfully logged in.
				</strong>

				<br /><br />

			<?php endif; ?>

			<small>Welcome to your administration panel! <br /> Use the links above to navigate.</small>

		</div>
	</body>
</html>