<?php
	include_once "sessions.php";
	include "weeklydata.php";
	include "display_functions.php";
	include "composition.php";

	$user = tblusers::get($_SESSION['userid']);
	$isimperial = $user->getIsImperial();

	$target = $_SERVER['PHP_SELF'];
	$limit = 20;
	$totrecs = tblweeklydata::getRecordCount();
	$pages = ceil($totrecs / $limit);
	$page = (isset($_GET['page']) ? preg_replace('#[^0-9]#i', '', $_GET['page']) : 1);
	$page = ($page < 1) ? 1 : (($page > $pages) ? $pages : $page);
	$start = ($page - 1) * $limit;
	$objects = ($totrecs < $limit) ? tblweeklydata::getAll() : tblweeklydata::getAll($start, $limit);
	$values = array();
	foreach($objects as $object) {
		$weight = $object->getWeight();
		$values['weight'][] = $weight;
		$waist = $object->getWaistSize();
		$values['waist'][] = $waist;
	}
	$str_weights = '[' . implode(',', $values['weight']) . ']';
	$str_waists = '[' . implode(',', $values['waist']) . ']';
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
		<title>Display Weekly Data</title>
		<?php
			require_once("load_libraries.php");
			loadJavascript("../scripts/loader.js");
			loadJQuery();
		?>
		<script>
			loadCSS("../css/list.css");
			require("../scripts/display_values.js");

			function displayValues() {
				var wtelems = document.getElementsByClassName("weight");
				var wselems = document.getElementsByClassName("waist");
				var weights = <?php echo $str_weights; ?>;
				var waists = <?php echo $str_waists; ?>;
				var isimp = $('#isimp').is(':checked');
				for(i = 0; i < wtelems.length; i++) {
					weights[i] = Number(weights[i]);
					waists[i] = Number(waists[i]);
					wtelems[i].innerHTML = (weights[i] == 0 ? "No Data" : display_value(weights[i], 0, isimp, true));
					wselems[i].innerHTML = (waists[i] == 0 ? "No Data" : display_value(waists[i], 3, isimp, true));
				}
			}

			window.onload = function() {
				displayValues();
	    		};
		</script>
	</head>
	<body>
<?php
	echo "\t\t<div id=\"lhead\">\n";
	echo "\t\t\t<h1 id=\"ltitle\">Weight and Waist Size Data For {$_SESSION['name']}</h1>\n";
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
				<p class="icheck">
					<input type="checkbox" id="isimp" name="isimp" value="Yes" onclick="displayValues()"<?php echo ($isimperial ? " checked" : ""); ?>>Display values in imperial units
				</p>
			<table>
				<tr class="dhead">
					<th class="hodd">Observation Date</th>
					<th class="heven">Weight</th>
					<th class="hodd">Waist Size</th>
					<th class="hedit"></th>
					<th class="hdelete"></th>
				</tr>
<?php
	foreach ($objects as $object) {
		$dataid = $object->getID();
		$weight = display::display_value($object->getWeight(), 1, $user->getIsImperial());
		$waistsize = display::display_value($object->getWaistSize(), 2, $user->getIsImperial());
		echo "\t\t\t\t<tr class=\"drow\">\n";
		echo "\t\t\t\t\t<td class=\"dodd\"><strong>{$object->getObsDate()->format('l jS F Y')}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven weight\"><strong>$weight</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dodd waist\"><strong>$waistsize</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dedit\"><a href=\"dataForm.php?id={$object->getid()}\"><img class=\"dicon\" src=\"../images/edit.png\" alt=\"Edit\"> Edit</a></td>\n";
		echo "\t\t\t\t\t<td class=\"ddelete\"><a href=\"deletedata.php?id={$object->getid()}\" onclick=\"return confirm('Do You Really Want To Delete This Record?')\"><img class=\"dicon\" src=\"../images/delete.png\" alt=\"Edit\"> Delete</a></td>\n";
		echo "\t\t\t\t</tr>\n";
	}
?>
			<tr><td colspan="7" class="daddrec"><a href="dataForm.php">Add New Record</a></td></tr>
			</table>
		</div>
		<div id="footer">
			<a href="dashboard.php" class="button">Back To Dashboard</a>
			<a class="button" href="logout.php">Log Out</a>
		</div>
	</body>
</html>

