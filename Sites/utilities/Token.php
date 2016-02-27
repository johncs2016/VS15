<?php
class Token {

	public static function generate() {
		return Session::put('token', md5(uniqid()));
	}

	public static function check($token) {
		$tokenName = TOKEN_NAME;
		
		if(Session::exists(TOKEN_NAME) && $token === Session::get(TOKEN_NAME)) {
			Session::delete(TOKEN_NAME);
			return true;
		}
		
		return false;
	}
}