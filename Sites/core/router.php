<?php
	class Router {
	
		public static function route($url) {
			// Split the URL into parts
			$url_array = array();
			if($url) $url_array = explode("/",$url);
			
			// The first part of the URL is the controller name
			$controller = isset($url_array[0]) ? $url_array[0] : '';
			array_shift($url_array);
			
			// The second part is the method name
			$action = isset($url_array[0]) ? $url_array[0] : '';
			array_shift($url_array);
			
			// The third part are the parameters
			$query_string = $url_array;
			
			// if controller is empty, redirect to default controller
			if(empty($controller)) {
				$controller = DEFAULT_CONTROLLER;
			}
			
			// if action is empty, redirect to index page
			if(empty($action)) {
				$action = DEFAULT_ACTION;
			}
			
			$controller_name = $controller;
			$controller = ucwords($controller);
			$dispatch = new $controller($controller_name,$action);
			
			if ((int)method_exists($controller, $action)) {
				call_user_func_array(array($dispatch,$action),$query_string);
			} else {
				Redirect::to(404);
			}
		}
	}
