<?php
	include (ROOT . DS . 'templates' . DS . 'header.php');
	
	if(Session::exists('home')) {
		echo Session::flash('home');
	} else {
		echo (string)(new PTag($title));
	}

	if(isset($logged_in) && $logged_in == "Yes") {
?>
		<a href="/Users/logout">Logout</a>
<?php
	} else {
?>
		<a href="/Users/login">Login</a>
		<a href="/Users/Register">Register</a>
<?php
	}

	include (ROOT . DS . 'templates' . DS . 'footer.php');
?>
