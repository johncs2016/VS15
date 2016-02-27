<?php
	include_once("sessions.php");
	include "recipient.php";
	include "rectypes.php";
	require_once("load_libraries.php");

	$user = tblusers::get($_SESSION['userid']);
	$details = tbldetails::get($user->getDetailsID());
	$target = $_SERVER['PHP_SELF'];
	$limit = 20;
	$totrecs = tblreportrecipients::getRecordCount();
	$pages = ceil($totrecs / $limit);
	$page = (isset($_GET['page']) ? preg_replace('#[^0-9]#i', '', $_GET['page']) : 1);
	$page = ($page < 1) ? 1 : (($page > $pages) ? $pages : $page);
	$start = ($page - 1) * $limit;
	$objects = ($totrecs < $limit) ? tblreportrecipients::getAll() : tblreportrecipients::getAll($start, $limit);
	$numrecs = count($objects);
	$centrePage = '';
	$prev = $page - 1;
	$prev2 = $prev - 1;
	$next = $page + 1;
	$next2 = $next + 1;
	if ($page == 1) {
	    	$centrePage .= "\t\t\t\t\t<li class=\"pagNumActive\">$page</li>\n";
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$next\" >$next </a></li>\n";
	}
	else if ($page == $pages) {
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$prev \">$prev</a></li>\n";
	    	$centrePage .= "\t\t\t\t\t<li class=\"pagNumActive\">$page</li>\n";
	}
	else if ($page > 2 && $page < ($pages - 1)) {
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$prev2\">$prev2</a></li>\n";
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$prev\">$prev</a></li>\n";
	    	$centrePage .= "\t\t\t\t\t<li class=\"pagNumActive\">$page</li>\n";
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$next\">$next</a></li>\n";
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$next2\">$next2</a></li>\n";
	}
	else if ($page > 1 && $page < $pages) {
	    	$centrePage .= "\t\t\t\t<li><a href=\"$target?page=$prev\">$prev</a></li>\n";
	    	$centrePage .= "\t\t\t\t<li class=\"pagNumActive\">$page</li>\n";
	    	$centrePage .= "\t\t\t\t<li><a href=\"$target?page=$next\">$next</a></li>\n";
	}
	$paginationDisplay = ""; // Initialize the pagination output variable

	if ($pages != 1) {
		$paginationDisplay .= "<li class=\"pagesum\">Page $page of $pages</li>\n";
		if ($page >= 4) {
	         	$paginationDisplay .=  "\t\t\t<li class=\"first\"><a href=\"$target?page=1\"> First</a></li>\n";
		}
		if ($page != 1) {
	         	$paginationDisplay .=  "\t\t\t<li class=\"prev\"><a href=\"$target?page=$prev\"> Prev</a></li>\n";
		}
		$paginationDisplay .= "\t\t\t<li>\n\t\t\t\t<ul class=\"paginationNumbers\">\n$centrePage\t\t\t\t</ul>\n\t\t\t</li>\n";
		if ($page != $pages) {
			$paginationDisplay .=  "\t\t\t<li class=\"next\"><a href=\"$target?page=$next\"> Next</a></li>\n";
		}
		if ($page < $pages - 2) {
			$paginationDisplay .=  "\t\t\t<li class=\"last\"><a href=\"$target?page=$pages\"> Last</a></li>\n";
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Display Steps Counts</title>
		<?php
			loadJavascript("../scripts/loader.js");
			loadJQuery();
		?>
		<script>
			loadCSS("../css/list.css");
		</script>
	</head>
	<body>
<?php
	echo "\t\t<div id=\"lhead\">\n";
	echo "\t\t\t<h1 id=\"ltitle\">List of Report Recipients For {$_SESSION['name']}</h1>\n";
	if (isset($_GET['action'])) {
		echo "<p class=\"action\">{$_GET['action']}</p>\n";
	} else {
		echo "<p class=\"action\"></p>\n";
	}
?>
			<p id="totrecs">Displaying Records <?php echo $start + 1; ?> to <?php echo $start + $numrecs; ?> out of a total of <?php echo $totrecs; ?> Results</p>
		</div>
		<div id="pagcon">
		<ul id="pagination">
			<?php echo "$paginationDisplay\n"; ?>
		</ul>
		</div>
		<div id="data">
			<table>
				<tr class="dhead">
					<th class="hodd">Type</th>
					<th class="heven">First Name</th>
					<th class="hodd">Last Name</th>
					<th class="heven">Email Address</th>
					<th class="hedit"></th>
					<th class="hdelete"></th>
				</tr>
<?php
	foreach ($objects as $object) {
		$rectype = tblrectypes::get($object->getRecID());
		$type = $rectype->getType();
		$recdet = tbldetails::get($object->getDetailsID());
		$firstname = $recdet->getFirstName();
		$lastname = $recdet->getLastName();
		$email = $recdet->getEmailAddress();
		echo "\t\t\t\t<tr class=\"drow\">\n";
		echo "\t\t\t\t\t<td class=\"dodd\"><strong>{$type}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven steps\"><strong>{$firstname}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dodd dist\"><strong>{$lastname}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven\"><strong>{$email}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dedit\"><a href=\"recipientsform.php?id={$object->getid()}\"><img class=\"dicon\" src=\"../images/edit.png\" alt=\"Edit\"> Edit</a></td>\n";
		echo "\t\t\t\t\t<td class=\"ddelete\"><a href=\"deleterecipients.php?id={$object->getid()}\" onclick=\"return confirm('Do You Really Want To Delete This Record?')\"><img class=\"dicon\" src=\"../images/delete.png\" alt=\"Edit\"> Delete</a></td>\n";
		echo "\t\t\t\t</tr>\n";
	}
?>
			<tr><td colspan="6" class="daddrec"><a href="recipientsform.php">Add New Record</a></td></tr>
			</table>
		</div>
		<div id="footer">
			<a href="../index.php" class="button">Back To Dashboard</a>
			<a class="button" href="logout.php">Log Out</a>
		</div>
	</body>
</html>

