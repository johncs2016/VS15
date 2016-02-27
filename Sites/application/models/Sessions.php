<?php

class Session extends Model {

	protected $_id;
	protected $_session_name;
	protected $_set_time;
	protected $_data;
	protected $_session_key;
	
	public function __construct($properties) {
		// Set Database Fields
		parent::__construct('tblsessions', $properties);

		// set our custom session functions.
	   session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc'));
	 
	   // This line prevents unexpected effects when using objects as save handlers.
	   register_shutdown_function('session_write_close');
	}
	
	public function getID() {
		return $this->_id;
	}
	
	public function setID($val) {
		$this->_id = $val;
	}
	
	public function getSetTime() {
		return $this->_set_time;
	}

	public function setSetTime($val) {
		$this->_set_time = $val;
	}

	public function getSessionName() {
		return $this->_session_name;
	}

	public function setSessionName($val) {
		$this->_session_name = $val;
	}

	public function getData() {
		return $this->_data;
	}
	
	public function setData($val) {
		$this->_data = $val;
	}

	public function getSessionKey() {
		return $this->_session_key;
	}

	public function setSessionKey($val) {
		$this->_session_key = $val;
	}

	public function start_session($secure) {
	   // Make sure the session cookie is not accessible via javascript.
	   $httponly = true;
	 
	   // Hash algorithm to use for the session. (use hash_algos() to get a list of available hashes.)
	   $session_hash = 'sha512';
	 
	   // Check if hash is available
	   if (in_array($session_hash, hash_algos())) {
		  // Set the has function.
		  ini_set('session.hash_function', $session_hash);
	   }
	   // How many bits per character of the hash.
	   // The possible values are '4' (0-9, a-f), '5' (0-9, a-v), and '6' (0-9, a-z, A-Z, "-", ",").
	   ini_set('session.hash_bits_per_character', 5);
	 
	   // Force the session to only use cookies, not URL variables.
	   ini_set('session.use_only_cookies', 1);
	 
	   // Get session cookie parameters 
	   $cookieParams = session_get_cookie_params(); 
	   // Set the parameters
	   session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
	   // Change the session name 
	   session_name($this->getSessionName());
	   // Now we cat start the session
	   session_start();
	   // This line regenerates the session and delete the old one. 
	   // It also generates a new encryption key in the database. 
	   session_regenerate_id(true); 
	}
	
	public function open() {
		self::connectToDatabase();
	}
	
	public function close() {
		self::disconnectFromDatabase();
	}
	
	public function read() {
		return $this->decrypt($this->getData(), $this->getKey());
	}
	
	public function write() {
		
	}
}