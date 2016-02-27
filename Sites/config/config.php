<?php
	// Show debug messages
	define ('DEBUG', true);
	
	// Database connection
	define('DB_DRIVER', 'mysql');
	define('DB_HOST', '127.0.0.1');
	define('DB_NAME', 'health');
	define('DB_USER', 'health');
	define('DB_PASS', '4Tya23QZZBTX5Zsy');
	
	// Website URL and path
	define('PATH', 'http://localhost/');
	define('WEBSITE_TITLE', 'My Website');
	
	// Default controller to load — homepage requests are sent to this controller
	define('DEFAULT_CONTROLLER', 'home');
	
	// Default action to run — homepage requests will run this action
	define('DEFAULT_ACTION', 'index');

	// Additional security info for sessions, cookies and tokens
	define('SESSION_NAME', 'user');
	define('TOKEN_NAME', 'token');
	define('COOKIE_NAME', 'hash');
	define('COOKIE_EXPIRY', 604800);

