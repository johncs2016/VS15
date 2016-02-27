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

			<?php if (!empty($users)) : ?>

				<?php foreach ($users as $user) : ?>

					<?php echo $user->username; ?>

					<small>
						(<a href="user_delete.php?id=<?php echo $user->user_id; ?>">delete</a>)<?php if (end($users) != $user) : ?>, <?php endif; ?>
					</small>

				<?php endforeach; ?>

			<?php else : ?>

				<p>There are no users.</p>

			<?php endif; ?>

			<br /><br />

			<h3>Add User</h3>

			<?php if (isset($error)) echo '<b><font color="#aa0000">' . $error . '</font></b><br /><br />'; ?>

			<form action="users.php" method="post">
				<input type="text" name="username" placeholder="Username" />
				<input type="password" name="password" placeholder="Password" />
				<input type="submit" value="Add User" />
			</form>

		</div>
	</body>
</html>