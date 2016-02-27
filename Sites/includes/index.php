<?php
	include_once("sessions.php");
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Welcome to the Secure area</title>
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;"/>
		<link rel="stylesheet" type="text/css" href="../css/default.css"/>
	</head>
	<body>
		<header><h1>Secure Area</h1></head>
		<p>This is one (of many potential) pages that reside in the secure area. All you need to remember to do is to include the <strong>sessions.php</strong> file, which handles the user authentication every time.</p>
		<p>Your User Details are as follows:</p>
		<ul>
			<?php foreach($_SESSION as $key => $value) { ?>
				<li><?php echo $key; ?> <strong><?php echo $value; ?></strong></li>
			<?php } ?>
		</ul>

		<footer>
			<a href="logout.php">logout</a>
		</footer>
	</body>
</html>


