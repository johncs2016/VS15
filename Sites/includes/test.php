<!DOCTYPE html>
<html>
	<head>
		<title>My test Website</title>
	</head>
	<body>
		<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
			<?php
				$now = new DateTime();
				$val = (isset($_POST['odate']) ? $_POST['odate'] : $now->format('d/m/Y'));
			?>
			<p class="dpicker">
				<label for="odate">Observation Date</label>
				<input type="text" id="odate" name="odate" value="<?= $val?>" placeholder="<?php echo $now->format('d/m/Y'); ?>">
				<span class="<?php echo isset($error_array['obsdate']) ? 'form_error' : 'form_hint'; ?>"><?php echo isset($error_array['obsdate']) ? $error_array['obsdate'] : '* Please select a date'; ?></span>
			</p>
			<p class="submit">
				<button type="submit" id="submit" class="button">Submit</button>
			</p>
		</form>
		<?php
			echo "<p>$val</p>";
		?>
	</body>
</html>
