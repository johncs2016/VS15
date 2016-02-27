<?php
	include (ROOT . DS . 'templates' . DS . 'header.php');
	
	if(Session::exists('home')) {
		echo Session::flash('home');
	}

	$fields = [];

	$fields[] = (new formFields("username", "User Name", (isset($values['username']) ? $values['username'] : null)))->textField();
	$span_field = (string)(new spanTag(null, "info", "6-12 chars"));
	$fields[] = (new formFields("password", "Password" . (string)$span_field))->passwordField();
	$fields[] = (new formFields("password_again", "Confirm Password"))->passwordField();
	$fields[] = (new formFields("name", "Name", (isset($values['name']) ? $values['name'] : null)))->textField();
	$fieldset = (string)(new fieldSet("Register New User", $fields));
	unset($fields);
	$fields = [];
	$fields[] = $fieldset;
	$fields[] = (string)(new inputTag("Hidden", null, null, null, Token::generate(), array("name" => "token")));
	$fields[] = (string)(new inputTag("Submit", null, null, null, "Register"));
	
	$form = new form(null, "box400", "registerform", "/Users/getRegister", true, $fields);

	echo $form;

	include (ROOT . DS . 'templates' . DS . 'footer.php');
?>
