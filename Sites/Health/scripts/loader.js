function require(fname) {
	document.write("<script src='/Health/scripts/" + fname + "'><\/script>\n");
}

function loadJQuery() {
	loadCSS("jquery-ui.css");
	require("jquery.js");
	require("jquery-ui.js");
	require("jquery.validate.js");
	require("additional-methods.js");
}

function loadCSS(fname) {
	document.write("<link rel='stylesheet' href='/Health/css/" + fname + "'>");
}

function relToFull(url){
	var a=document.createElement("a");
	a.href=url;
	return a.href;
}

function scriptLoaded(fName) {
	var scripts = document.getElementsByTagName('script');
	for(i in scripts) {
		if(scripts[i].src == fName) {
			return true;
		}
	}
	return false;
}


