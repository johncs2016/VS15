<?php
	$srv = "{imap.gmail.com:993/imap/ssl}";
	$conn = imap_open($srv, "johncs2008@gmail.com", "rdotbjlngiazfltz");
	$boxes = imap_getmailboxes($conn, $srv, '*');
	echo '<pre>';
	print_r($boxes);
	echo '</pre>';
	imap_close($conn);
?>
