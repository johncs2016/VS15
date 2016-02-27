<?php
	// Load configuration and helper functions
	
	require_once(ROOT . DS . 'config' . DS . 'config.php');
	require_once(ROOT . DS . 'core' . DS . 'functions.php');
	
	// Autoload classes
	function __autoload($className) {
		if (file_exists(ROOT . DS . 'core' . DS . strtolower($className) . '.php')) {
			require_once(ROOT . DS . 'core' . DS . strtolower($className) . '.php');
		} elseif (file_exists(ROOT . DS . 'library' . DS . strtolower($className) . '.php')) {
			require_once(ROOT . DS . 'library' . DS . strtolower($className) . '.php');
		} elseif (file_exists(ROOT . DS . 'utilities' . DS . strtolower($className) . '.php')) {
			require_once(ROOT . DS . 'utilities' . DS . strtolower($className) . '.php');
		} elseif (file_exists(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php')) {
			require_once(ROOT . DS . 'application' . DS . 'controllers' . DS . strtolower($className) . '.php');
		} else if (file_exists(ROOT . DS . 'application' . DS . 'models' . DS . $className . '.php')) {
			require_once(ROOT . DS . 'application' . DS . 'models' . DS . $className . '.php');
		}
	}
	
	// Route the request
	Router::route($url);

