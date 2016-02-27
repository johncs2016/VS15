<?php
	include_once("sessions.php");
	include "steps.php";
	include "display_functions.php";
	include "composition.php";
	require_once("body_measurements.php");
	require_once("load_libraries.php");

	$user = tblusers::get($_SESSION["userid"]);
	$details = tbldetails::get($user->getDetailsID());
	$bm_object = new bodyMeasurements($user->getID());
	$stepsize = $bm_object->getStepSize();
	$isimperial = $user->getIsImperial();
	$target = $_SERVER['PHP_SELF'];
	$limit = 20;
	$totrecs = tblstepscounts::getRecordCount();
	$pages = ceil($totrecs / $limit);
	$page = (isset($_GET['page']) ? preg_replace('#[^0-9]#i', '', $_GET['page']) : 1);
	$page = ($page < 1) ? 1 : (($page > $pages) ? $pages : $page);
	$start = ($page - 1) * $limit;
	$objects = ($totrecs < $limit) ? tblstepscounts::getAll() : tblstepscounts::getAll($start, $limit);
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
			require("../scripts/display_values.js");

			function displayDistances() {
				var dists = document.getElementsByClassName("dist");
				var steps = document.getElementsByClassName("steps");
				var size = Number(<?php echo $stepsize; ?>);
				var isimp = $('#isimp').is(':checked');
				for(i = 0; i < steps.length; i++) {
					if (steps[i] === ""){
						dists[i].innerHTML="";
					} else {
						var dist = Number(steps[i].firstChild.innerHTML) * size / 1000;
						dists[i].innerHTML = display_value(dist, 2, isimp, true);
					}
				}
			}

			jQuery(function($){ //on document.ready
				displayDistances();
	    		})
		</script>
	</head>
	<body>
<?php
	echo "\t\t<div id=\"lhead\">\n";
	echo "\t\t\t<h1 id=\"ltitle\">List of Steps Counts For {$details->fullname()}</h1>\n";
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
					<input type="checkbox" id="isimp" name="isimp" value="Yes" onclick="displayDistances()"<?php echo ($isimperial ? " checked" : ""); ?>>Display distances in miles
				</p>
			<table>
				<tr class="dhead">
					<th class="hodd">Observation Date</th>
					<th class="heven">Number Of Steps Walked</th>
					<th class="hodd">Distance Walked</th>
					<th class="hedit"></th>
					<th class="hdelete"></th>
				</tr>
<?php
	foreach ($objects as $object) {
		echo "\t\t\t\t<tr class=\"drow\">\n";
		echo "\t\t\t\t\t<td class=\"dodd\"><strong>{$object->getObsDate()->format('l jS F Y')}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven steps\"><strong>{$object->getStepsCount()}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dodd dist\"><strong></strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dedit\"><a href=\"stepsform.php?id={$object->getid()}\"><img class=\"dicon\" src=\"../images/edit.png\" alt=\"Edit\"> Edit</a></td>\n";
		echo "\t\t\t\t\t<td class=\"ddelete\"><a href=\"deletesteps.php?id={$object->getid()}\" onclick=\"return confirm('Do You Really Want To Delete This Record?')\"><img class=\"dicon\" src=\"../images/delete.png\" alt=\"Edit\"> Delete</a></td>\n";
		echo "\t\t\t\t</tr>\n";
	}
?>
			<tr><td colspan="6" class="daddrec"><a href="stepsform.php">Add New Record</a></td></tr>
			</table>
		</div>
		<div id="footer">
			<a href="weekly_steps.php" class="button">Weekly Steps Counts</a>
			<a href="/Health/index.php" class="button">Back To Dashboard</a>
			<a class="button" href="logout.php">Log Out</a>
		</div>
	</body>
</html>

