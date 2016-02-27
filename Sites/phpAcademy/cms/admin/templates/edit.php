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

			<?php if (isset($error)) echo '<b><font color="#aa0000">' . $error . '</font></b><br /><br />'; ?>

			<form action="edit.php?id=<?php echo $id; ?>" method="post">
				Title: <input type="text" name="title" value="<?php echo $title; ?>" /><br />
				<textarea style="margin-top: 10px;" name="content" cols="40" rows="15"><?php echo $content; ?></textarea><br />
				<input type="submit" value="Submit" />
			</form>

		</div>
	</body>
</html>