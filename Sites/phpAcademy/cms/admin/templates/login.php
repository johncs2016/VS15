<!doctype html>
<html lang="en">
	<head>
		<title>CMS Tutorial</title>

		<link rel="stylesheet" href="../assets/css/style.css" />
	</head>

	<body>
		<div class="container">
			<a href="index.php" id="logo">CMS Tutorial</a> &nbsp; &nbsp; <small><a href="#">admin</a></small><br /><br />

			<?php if (isset($error)) echo '<b><font color="#aa0000">' . $error . '</font></b><br /><br />'; ?>

			<form action="index.php" method="post">
				<input type="text" name="username" placeholder="Username" />
				<input type="password" name="password" placeholder="Password" />

				<input type="submit" class="btn" value="Sign in" />
			</form>

		</div>
	</body>
</html>