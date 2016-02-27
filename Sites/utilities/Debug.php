<?php
	class Debug {
		
		private function __construct () {
			
		}
		
		public static function VarDump($object) {
			echo '<pre>';
			var_dump($object);
			echo '</pre>';
		}
		
		public static function PrintR($object) {
			echo '<pre>';
			print_r($object);
			echo '</pre>';
		}
		
		public static function PrintVariable($text) {
			$tag = new PTag($text);
			echo (string)$tag;
		}
	}