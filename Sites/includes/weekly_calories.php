<?php
	include_once "sessions.php";
	include("cardio.php");
	include("display_functions.php");
	require_once("load_libraries.php");

	function target_achieved($calories, $target) {
		return ($calories >= $target);
	}
	
	function str_achieved($calories, $target) {
		return (target_achieved($calories, $target) == true ? "YES" : "NO");
	}

	function getWeeklyCalories($start = null, $rowcount = null) {
	
		$sql = "SELECT " . display::week_sql(true, true) . ", " . display::week_sql(false, true) . ", SUM(calories) AS total_calories FROM tblcardio WHERE userid = " . $_SESSION['userid'] . " " . display::group_sql(true) . " HAVING MAX(calories) > 0";
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
		$user = tblusers::get($_SESSION['userid']);
		$target_calories = $user->getTargetCalories();
		foreach ($db->query($sql . $limit, PDO::FETCH_ASSOC) as $row) {
			$values = array();
			foreach ($row as $key => $value) {
				$results['_'.$key] = $value;
			}
			$results['_target_achieved'] = str_achieved($results['_total_calories'], $target_calories);
			$return[] = $results;
		}

		$db = null;
		return $return;
	}

	$user = tblusers::get($_SESSION['userid']);
	$target = $_SERVER['PHP_SELF'];
	$limit = 20;
	$records = getWeeklyCalories();
	$totrecs = count($records);
	$pages = ceil($totrecs / $limit);
	$page = (isset($_GET['page']) ? preg_replace('#[^0-9]#i', '', $_GET['page']) : 1);
	$page = ($page < 1) ? 1 : (($page > $pages) ? $pages : $page);
	$start = ($page - 1) * $limit;
	if($totrecs >= $limit) $records = getWeeklyCalories($start, $limit);
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
		<title>Display Weekly Calories</title>
		<?php
			loadJQuery();
			loadJavascript("../scripts/loader.js");
		?>
		<script>
			loadCSS("../css/list.css");
		</script>
	</head>
	<body>
<?php
	echo "\t\t<div id=\"lhead\">\n";
	echo "\t\t\t<h1 id=\"ltitle\">List of Weekly Calories For " . $_SESSION['name'] . "</h1>\n";
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
					<th class="hodd">Week Commencing</th>
					<th class="heven">Total Calories</th>
					<th class="hodd">Target Achieved</th>
				</tr>
<?php
	foreach ($records as $record) {
		$startdate = DateTime::createFromFormat('Y-m-d', $record["_week_commencing"]);
		$enddate = DateTime::createFromFormat('Y-m-d', $record["_week_ending"]);
		echo "\t\t\t\t<tr class=\"drow\">\n";
		echo "\t\t\t\t\t<td class=\"dodd\"><strong>{$startdate->format('jS F Y')}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven\"><strong>{$record["_total_calories"]}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dodd\"><strong>{$record["_target_achieved"]}</strong></td>\n";
		echo "\t\t\t\t</tr>\n";
	}
?>
			</table>
		</div>
		<div id="footer">
			<a href="display_cardio.php" class="button">Back To Cardio Display</a>
			<a href="dashboard.php" class="button">Back To Dashboard</a>
			<a class="button" href="logout.php">Log Out</a>
		</div>
	</body>
</html>

