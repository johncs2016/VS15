<?php
	function loadJavascript($url) {
		echo "\t\t<script src=\"/Health/scripts/$url\"></script>\n";
	}

	function loadCSS($url, $media = null) {
		echo "\t\t<link rel=\"stylesheet\" type=\"text/css\"" . ($media == null ? "" : " media=\"$media\"") . " href=\"/Health/css/$url\">\n";
	}

	function loadJQuery() {
		loadCSS("jquery-ui.css");
		loadJavaScript("jquery.js");
		loadJavaScript("jquery-ui.js");
		loadJavaScript("jquery.validate.js");
		loadJavaScript("additional-methods.js");
	}
?>
