<?php
require 'core/init.php';

if(!$username = Input::get('user')) {
	Redirect::to('index.php');
} else {
	$user = new User($username);

	if(!$user->exists()) {
		Redirect::to(404);
	} else {
		$root = $user->data();
	}
	?>

	<h3><?php echo escape($root->username); ?></h3>
	<p>Full name: <?php echo escape($root->name); ?></p>

	<?php
}
?>