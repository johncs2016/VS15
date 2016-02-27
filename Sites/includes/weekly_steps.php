<?php
	include_once "sessions.php";
	include("steps.php");
	include("display_functions.php");
	include("composition.php");
	include("body_measurements.php");
	require_once("load_libraries.php");

	function getWeeklySteps($start = null, $rowcount = null) {

		$sql = "SELECT " . display::week_sql(true, false) . ", " . display::week_sql(false, false) . ", SUM(numberofstepswalked) AS total_steps, ROUND(AVG(numberofstepswalked)) AS average_steps FROM tblstepscounts WHERE userid = " . $_SESSION['userid'] . " " . display::group_sql(false);
		$db = pdoClass::getConnection();
		$query = $db->prepare($sql);
		$query->execute();
		$results = $query->fetchAll(PDO::FETCH_ASSOC);
		$total = count($results);
		if(!isset($start) && !isset($rowcount)) {
			$start = 1;
			$rowcount = $total;
		}
		elseif (!isset($start) || !isset($rowcount)) {
			$rowcount = (isset($start) ? $start : $rowcount);
			$start = 1;
		}
		
		if ($start < 1) {
			$start = 1;
		}
		
		$end = $start + $rowcount - 1;
		if ($end > $total) {
			$rowcount = $total - $start;
		}
		
		if ($start == 1 && $end == $total) {
			$limit = "";
		}
		else {
			$limit = " LIMIT ";
			if ($start == 1) {
				$limit .= $rowcount;
			}
			else {
				$limit .= $start . ", " . $rowcount;
			}
		}
		$results = array();
		foreach ($db->query($sql . $limit, PDO::FETCH_ASSOC) as $row) {
			$values = array();
			foreach ($row as $key => $value) {
				$results['_'.$key] = $value;
			}
			$return[] = $results;
		}

		$db = null;
		return $return;
	}

	$user = tblusers::get($_SESSION['userid']);
	$bm_object = new bodyMeasurements($user->getID());
	$stepsize = $bm_object->getStepSize();
	$isimp = $user->getIsImperial();
	$target = $_SERVER['PHP_SELF'];
	$limit = 20;
	$records = getWeeklySteps();
	$totrecs = count($records);
	$pages = ceil($totrecs / $limit);
	$page = (isset($_GET['page']) ? preg_replace('#[^0-9]#i', '', $_GET['page']) : 1);
	$page = ($page < 1) ? 1 : (($page > $pages) ? $pages : $page);
	$start = ($page - 1) * $limit;
	if($totrecs >= $limit) $records = getWeeklySteps($start, $limit);
	$numrecs = count($records);
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
		<title>Display Weekly Steps Counts</title>
		<?php
			loadJQuery();
			loadJavascript("../scripts/loader.js");
		?>
		<script>
			loadCSS("../css/list.css");
			require("../scripts/display_values.js");

			function displayDistance(flag, size, isimp) {
				var totavg = (flag ? "tot" : "avg");
				var stepclass = totavg + "steps";
				var distclass = totavg + "dist";
				var steps = document.getElementsByClassName(stepclass);
				var dists = document.getElementsByClassName(distclass);
				for(i = 0; i < steps.length; i++) {
					if (steps[i] === ""){
						dists[i].innerHTML="";
					} else {
						var dist = Number(steps[i].firstChild.innerHTML) * size / 1000;
						dists[i].innerHTML = display_value(dist, 2, isimp, true);
					}
				}
			}

			function displayDistances() {
				var size = Number(<?php echo $stepsize; ?>);
				var isimp = $('#isimp').is(':checked');
				displayDistance(true, size, isimp);
				displayDistance(false, size, isimp);
			}

			jQuery(function($){ //on document.ready
				displayDistances();
	    		})
		</script>
	</head>
	<body>
<?php
	echo "\t\t<div id=\"lhead\">\n";
	echo "\t\t\t<h1 id=\"ltitle\">List of Weekly Steps Counts For " . $_SESSION['name'] . "</h1>\n";
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
				<input type="checkbox" id="isimp" name="isimp" value="Yes" onclick="displayDistances()"<?php echo ($isimp ? " checked" : ""); ?>>Display distances in miles
			</p>
			<table>
				<tr class="dhead">
					<th class="hodd">Week Commencing</th>
					<th class="heven">Total Steps Counts</th>
					<th class="hodd">Total Distance</th>
					<th class="heven">Average Steps Counts</th>
					<th class="hodd">Average Distance</th>
				</tr>
<?php
	foreach ($records as $record) {
		$startdate = DateTime::createFromFormat('Y-m-d', $record["_week_commencing"]);
		$enddate = DateTime::createFromFormat('Y-m-d', $record["_week_ending"]);
		echo "\t\t\t\t<tr class=\"drow\">\n";
		echo "\t\t\t\t\t<td class=\"dodd\"><strong>{$startdate->format('jS F Y')}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven totsteps\"><strong>{$record["_total_steps"]}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dodd totdist\"><strong></strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven avgsteps\"><strong>{$record["_average_steps"]}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dodd avgdist\"><strong></strong></td>\n";
		echo "\t\t\t\t</tr>\n";
	}
?>
			</table>
		</div>
		<div id="footer">
			<a href="display_steps.php" class="button">Back To Steps Display</a>
			<a href="dashboard.php" class="button">Back To Dashboard</a>
			<a class="button" href="logout.php">Log Out</a>
		</div>
	</body>
</html>

