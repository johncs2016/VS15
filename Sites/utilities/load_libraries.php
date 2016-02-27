<?php
	class load_libraries {
		public static function loadJavascript($url) {
			echo "\t\t<script src=\"$url\"></script>\n";
		}

		public static function loadCSS($url, $media = null) {
			echo "\t\t<link rel=\"stylesheet\" type=\"text/css\"" . ($media == null ? "" : " media=\"$media\"") . " href=\"$url\">\n";
		}

		public static function loadJQuery() {
			self::loadCSS("/MVC/public/css/jquery-ui.css");
			self::loadJavaScript("/MVC/public/scripts/jquery.js");
			self::loadJavaScript("/MVC/public/scripts/jquery-ui.js");
			self::loadJavaScript("/MVC/public/scripts/jquery.validate.js");
			self::loadJavaScript("/MVC/public/scripts/additional-methods.js");
		}
	}
?>