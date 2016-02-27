<!DOCTYPE  html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;"/>
		<title><?=$title; ?></title>
		<?php
			if(isset($cssFiles) && !empty($cssFiles)) {
				foreach ($cssFiles as $value) {
					load_libraries::loadCSS($value);
				}
			}

			if(isset($javascriptFiles) && !empty($javascriptFiles)) {
				foreach ($javascriptFiles as $value) {
					load_libraries::loadJavaScript($value);
				}
			}

			if(isset($loadJQuery) && ($loadJQuery == true)) {
				load_libraries::loadJQuery();
			}
		?>
	</head>
	<body>
