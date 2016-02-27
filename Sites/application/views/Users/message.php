<?php
	include '../app/templates/header.php';

	if(Session::exists('home')) {
		echo '<p>', Session::flash('home'), '</p>';
	}

	include '../app/templates/footer.php';
?>