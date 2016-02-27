<?php

	class Users extends Controller {

	public function __construct($controller,$action) {
			// Load core controller functions
			parent::__construct($controller, $action);
			
			// Load models
			$this->load_model('User');
		}
		public function index() {
			$this->get_view()->set('title', 'Home Page');
			$this->get_view()->render('home/index');
		}

		public function login() {
			$user = $this->get_model('User');
			if ($user->isLoggedIn()) {
				Session::flash('home', 'You are already logged in');
				Redirect::to('/home/index');
			} else {
				$this->get_view()->set('title', 'Login Page');
				$this->get_view()->set('name', '');
				$this->get_view()->set('cssFiles', ['/css/default.css']);
				$this->get_view()->render('Users/login');
			}
		}
		
		public function logout() {
			$user = $this->get_model('User');
			if ($user->isLoggedIn()) {
				$user->logout();
				Session::flash('home', 'You have been now been logged out');
			} else {
				Session::flash('home', 'You have not been logged in');
			}
			Redirect::to('/home/index');
		}

		public function register() {
			$user = $this->get_model('User');
			if ($user->isLoggedIn()) {
				Session::flash('home', 'You are already registered and logged in');
				Redirect::to('/home/index');
			} else {
				$this->get_view()->set('title', 'Register User');
				$this->get_view()->set('name', '');
				$this->get_view()->set('cssFiles', ['/css/default.css']);
				$this->get_view()->render('Users/register');
			}
		}
		
		public function getLogin() {
			
			if(Input::exists()) {

				if(Token::check(Input::get('token'))) {

					$validate = new Validate();
					$validation = $validate->check($_POST, array(
						'username' => array(
							'required' => true,
							'min' => 2,
							'max' => 20),
						'password' => array(
							'required' => true,
							'min' => 6)
					));
					
					if($validation->passed()) {
						$user = $this->get_model('User');
						$remember = (Input::get('remember') === 'on') ? true : false;

						$login = $user->login(Input::get('username'), Input::get('password'), $remember);

						if($login) {
							Session::flash('home', 'You have been successfully logged in as ' . htmlentities(Input::get('username')));
							Redirect::to('/home/index');
						} else {
							Session::flash('home', 'Sorry, that username and password wasn\'t recognised.');
							$this->get_view()->set('title', 'Login Page');
							$this->get_view()->set('name', '');
							$this->get_view()->set('cssFiles', ['/css/default.css']);
							$this->get_view()->set('values', [
									'username'	=>	htmlentities(Input::get('username')),
									'remember'	=>	Input::get('remember')
								]);
							$this->get_view()->render('Users/login');
						}
					} else {
						Session::flash('home', implode('', $validate->errors()));
						$this->get_view()->set('title', 'Login Page');
						$this->get_view()->set('name', '');
						$this->get_view()->set('cssFiles', ['/css/default.css']);
						$this->get_view()->set('values', [
								'username'	=>	htmlentities(Input::get('username')),
								'remember'	=>	Input::get('remember')
							]);
						$this->get_view()->render('Users/login');
					}
				}
			}
		}

		public function getRegister() {

			if(Input::exists()) {
				if(Token::check(Input::get('token'))) {

					$validate = new Validate();
					$validation = $validate->check($_POST, array(
						'username' => array(
							'required' => true,
							'min' => 2,
							'max' => 20,
							'unique' => 'users'),
						'password' => array(
							'required' => true,
							'min' => 6),
						'password_again' => array(
							'required' => true,
							'matches' => 'password'),
						'name' => array(
							'required' => false,
							'min' => 2,
							'max' => 50)
					));
					
					if($validation->passed()) {
						$user = $this->model('User');

						$salt = Hash::salt(32);

						try {
							$user->create(array(
								'username' 	=> Input::get('username'),
								'password' 	=> Hash::make(Input::get('password'), $salt),
								'salt'		=> $salt,
								'name' 		=> Input::get('name'),
								'joined'	=> date('Y-m-d H:i:s'),
								'group_id'		=> 1
							));

							Session::flash('home', 'You have been registered and can now log in!');
							Redirect::to('/home/index');

						} catch(Exception $e) {
							die($e->getMessage());
						}

					} else {
						Session::flash('home', implode('', $validate->errors()));
						$this->get_view()->set('title', 'Register User');
						$this->get_view()->set('name', '');
						$this->get_view()->set('cssFiles', ['/css/default.css']);
						$this->get_view()->set('values', [
								'username'	=>	htmlentities(Input::get('username')),
								'name'	=>	htmlentities(Input::get('name'))
							]);
						$this->get_view()->render('Users/register');
					}
				}
			}
		}
	}
?>