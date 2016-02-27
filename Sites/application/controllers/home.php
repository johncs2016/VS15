<?php

class Home extends Controller {

	public function __construct($controller,$action) {
		// Load core controller functions
		parent::__construct($controller, $action);
		
		// Load models
		$this->load_model('User');
	}

	public function index() {
		// Display Home Page
		$this->home();
	}
	
	public function home() {
		// get user model
		$user = $this->get_model('User');

		// Set view variables
		$this->get_view()->set('title', 'Welcome to my website');
		$this->get_view()->set('logged_in', ($user->isLoggedIn() ? "Yes" : "No"));

		// Render view
		$this->get_view()->render('home/index');
	}
}