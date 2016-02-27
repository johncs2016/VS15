<?php
	class validator {
			
		private $error_array = array();
		
		public function getAllErrors() {
			return $this->error_array;
		}
		
		public function getErrors($field) {
			return isset($this->error_array[$field]) ? $this->error_array[$field] : null;
		}
		
		public function setErrors($field, $message) {
			if (isset($this->error_array[$field])) $this->error_array[$field] .= "\n"; else $this->error_array[$field] = '';
			$this->error_array[$field] .= $message;
		}

		public function validate_string($field, &$value, $required = false, $max_length = null) {
			$value = trim($value);
			$value = stripslashes($value);
			$value = htmlspecialchars($value);
			$value = filter_var($value, FILTER_SANITIZE_STRING);
			if (empty($value) && $required) {
				$this->setErrors($field, "Please enter a valid ".$field.".");
				return false;
			}

			if ($max_length != null) {
				if (strlen($value) > $max_length) {
					$this->setErrors($field, "Length of ".$field." must be less than ".$max_length." characters.");
					return false;
				}
			}
			return !isset($this->error_array[$field]);
		}
		
		public function validate_float($field, &$value, $required = false, $min_value = null, $max_value = null) {
			$value = trim($value);
			$value = stripslashes($value);
			$value = htmlspecialchars($value);
			if (empty($value) && $required) {
				$this->setErrors($field, "Please enter a valid ".$field.".");
				return false;
			}
			
			if (!is_numeric($value)) {
				$this->setErrors($field, "Please enter a valid ".$field.".");
				return false;
			}
			
			if ($min_value != null) {
				if ($value < $min_value) {
					$this->setErrors($field, "Value of ".$field." must be greater or equal to than ".$min_value.".");
					return false;
				}
			}

			if ($max_value != null) {
				if ($value < $max_value) {
					$this->setErrors($field, "Value of ".$field." must be less than or equal to than ".$max_value.".");
					return false;
				}
			}
			
			return true;
		}
		
		public function validate_integer($field, &$value, $required = false, $min_value = null, $max_value = null) {
			if ($min_value == null) $min_value = (-1 * PHP_INT_MAX) - 1;
			if ($max_value == null) $max_value = PHP_INT_MAX;
			$value = trim($value);
			$value = stripslashes($value);
			$value = htmlspecialchars($value);
			if (empty($value) && $required) {
				$this->setErrors($field, "Please enter a valid ".$field.".");
				return false;
			}
			
			if (!is_numeric($value)) {
				$this->setErrors($field, "Please enter a valid ".$field.".");
				return false;
			}
			
			$options = array(
				'options'	=>	array(
					'min_range'	=>	$min_value,
					'max_range'	=>	$max_value
				)
			);

			if (!is_numeric(filter_var($value, FILTER_VALIDATE_INT, $options))) {
				$this->setErrors($field, "Please enter a valid ".$field.".");
				return false;
			}
			
			return true;
		}
		
		public function validate_email($field, &$value, $max_length = null) {
			$value = trim($value);
			$value = stripslashes($value);
			$value = htmlspecialchars($value);
			if (empty($value)) {
				$this->setErrors($field, "Please enter a valid email address");
				return false;
			}
			
			$value = filter_var($value, FILTER_SANITIZE_EMAIL);
		

			if ($max_length != null) {
				if (strlen($value) > $max_length) {
					$this->setErrors($field, "Length of ".$field." must be less than ".$max_length." characters.");
					return false;
				}
			}

			if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
				$this->setErrors($field, "Please enter a valid email address");
				return false;
			}
			
			return true;
		}

		public function validate_url($field, &$value) {
			$value = trim($value);
			$value = stripslashes($value);
			$value = htmlspecialchars($value);
			if (empty($value)) {
				$this->setErrors($field, "Please enter a valid URL");
				return false;
			}
			
			$value = filter_var($value, FILTER_SANITIZE_URL);
		
			if (!filter_var($field, FILTER_VALIDATE_URL)) {
				$this->setErrors($field, "Please enter a valid URL");
				return false;
			}
			
			return true;
		}
		
		public function validate_date($field, &$value, $required = false) {
			$value = trim($value);
			$value = stripslashes($value);
			$value = htmlspecialchars($value);
			if (empty($value)) {
				if ($required) $this->setErrors($field, "Please enter a valid Date");
				return !$required;
			}
			
			try {
				$value = DateTime::createFromFormat('d/m/Y', $value);
			}
			catch (exception $e) {
				$this->setErrors($field, "Please enter a valid Date");
				return false;
			}
			$date_array = array(
				'Day'	=>	$value->format('d'),
				'Month'	=>	$value->format('m'),
				'Year'	=>	$value->format('y')
			);
			if (checkdate($date_array['Month'], $date_array['Day'], $date_array['Year'])) {
				$value = $value->format('Y-m-d');
				return true;
			}
			else {
				$this->setErrors($field, "Please enter a valid Date");
				return false;
			}
		}
	}