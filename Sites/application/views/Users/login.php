<?php
	include (ROOT . DS . 'templates' . DS . 'header.php');
	
	if(Session::exists('home')) {
		echo Session::flash('home');
	}
	
	$fields = [];

	$fields[] = (new formFields("username", "User Name", (isset($values['username']) ? $values['username'] : null)))->textField();
	$span_field = (string)(new spanTag(null, "info", "6-12 chars"));
	$fields[] = (new formFields("password", "Password" . null))->passwordField();
	$fields[] = (new formFields("remember", "Remember Me", (isset($values['remember']) ? $values['remember'] : 'on')))->checkBoxField();
	$fieldset = (string)(new fieldSet("Login", $fields));
	unset($fields);
	$fields = [];
	$fields[] = $fieldset;
	$fields[] = (string)(new inputTag("Hidden", null, null, null, Token::generate(), array("name" => "token")));
	$fields[] = (string)(new inputTag("Submit", null, null, null, "Login"));
	
	$form = new form(null, "box400", "loginform", "/Users/getLogin", true, $fields);

	echo $form;

	include (ROOT . DS . 'templates' . DS . 'footer.php');
?>
