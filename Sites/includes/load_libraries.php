<?php
	function loadJavascript($url) {
		echo "<script src=\"$url\"></script>";
	}

	function loadCSS($url, $media = null) {
		echo "<link rel=\"stylesheet\" type=\"text/css\"" . ($media == null ? "" : " media=\"$media\"") . " href=\"$url\">";
	}

	function loadJQuery() {
		loadCSS("../css/jquery-ui.css");
		loadJavaScript("../scripts/jquery.js");
		loadJavaScript("../scripts/jquery-ui.js");
	}
?>