<?php
	include_once("sessions.php");
	include "cardio.php";
	include "cardioequipment.php";
	include "cardioprogs.php";
	include "venues.php";
	include("validator.php");
	require_once("load_libraries.php");

	$mindate = DateTime::CreateFromFormat('Y-m-d H:i:s', tblcardio::getMinMaxValue('obsdate', false, false, true) . ' 00:00:00');
	$maxdate = DateTime::CreateFromFormat('Y-m-d H:i:s', tblcardio::getMinMaxValue('obsdate', false, false, false) . ' 00:00:00');
	$props = array(
		"_id"			=>	0,
		"_equipname"	=>	"All Equipment",
		"_km"			=>	0
	);
	$all_equipment = array();
	$all_equipment[] = new tblcardioequipment($props);
	$all_equipment[0]->setID(0);
	$equipment = tblcardioequipment::getAll();
	foreach ($equipment as $obj) {
		$all_equipment[] = $obj;
	}
	$where = array();
	if (isset($_GET['equip']) && ($_GET['equip'] != 0)) $where['equipid'] = $_GET['equip'];
	if (isset($_GET['sdate']) && ($_GET['sdate'] != '')) {
		$sdate = DateTime::CreateFromFormat("d F Y", $_GET['sdate']);
		$where['sdate'] = $sdate->format("Y-m-d");
	}
	if (isset($_GET['edate']) && ($_GET['edate'] != '')) {
		$edate = DateTime::CreateFromFormat("d F Y", $_GET['edate']);
		$where['edate'] = $edate->format("Y-m-d");
	}
	if (empty($where)) $where = null;
	$user = tblusers::get($_SESSION["userid"]);
	$details = tbldetails::get($user->getDetailsID());
	$target = $_SERVER['PHP_SELF'];
	$baseurl = $target . (empty($_GET) ? '' : ('?' . buildURL()));
	$limit = 20;
	$totrecs = tblcardio::getRecordCount($where);
	$pages = ceil($totrecs / $limit);
	$page = (isset($_GET['page']) ? preg_replace('#[^0-9]#i', '', $_GET['page']) : 1);
	$page = ($page < 1) ? 1 : (($page > $pages) ? $pages : $page);
	$start = ($page - 1) * $limit;
	$objects = ($totrecs < $limit) ? tblcardio::getAll(0 , $totrecs, $where) : tblcardio::getAll($start, $limit, $where);
	$numrecs = count($objects);
	$centrePage = '';
	$prev = $page - 1;
	$prev2 = $prev - 1;
	$next = $page + 1;
	$next2 = $next + 1;
	if ($page == 1) {
	    	$centrePage .= "\t\t\t\t\t<li class=\"pagNumActive\">$page</li>\n";
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$next&" . buildURL() . "\" >$next </a></li>\n";
	}
	else if ($page == $pages) {
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$prev&" . buildURL() . "\">$prev</a></li>\n";
	    	$centrePage .= "\t\t\t\t\t<li class=\"pagNumActive\">$page</li>\n";
	}
	else if ($page > 2 && $page < ($pages - 1)) {
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$prev2&" . buildURL() . "\">$prev2</a></li>\n";
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$prev&" . buildURL() . "\">$prev</a></li>\n";
	    	$centrePage .= "\t\t\t\t\t<li class=\"pagNumActive\">$page</li>\n";
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$next&" . buildURL() . "\">$next</a></li>\n";
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$next2&" . buildURL() . "\">$next2</a></li>\n";
	}
	else if ($page > 1 && $page < $pages) {
	    	$centrePage .= "\t\t\t\t<li><a href=\"$target?page=$prev&" . buildURL() . "\">$prev</a></li>\n";
	    	$centrePage .= "\t\t\t\t<li class=\"pagNumActive\">$page</li>\n";
	    	$centrePage .= "\t\t\t\t<li><a href=\"$target?page=$next&" . buildURL() . "\">$next</a></li>\n";
	}
	$paginationDisplay = ""; // Initialize the pagination output variable

	if ($pages != 1) {
		$paginationDisplay .= "<li class=\"pagesum\">Page $page of $pages</li>\n";
		if ($page >= 4) {
	         	$paginationDisplay .=  "\t\t\t<li class=\"first\"><a href=\"$target?page=1&" . buildURL() . "\"> First</a></li>\n";
		}
		if ($page != 1) {
	         	$paginationDisplay .=  "\t\t\t<li class=\"prev\"><a href=\"$target?page=$prev&" . buildURL() . "\"> Prev</a></li>\n";
		}
		$paginationDisplay .= "\t\t\t<li>\n\t\t\t\t<ul class=\"paginationNumbers\">\n$centrePage\t\t\t\t</ul>\n\t\t\t</li>\n";
		if ($page != $pages) {
			$paginationDisplay .=  "\t\t\t<li class=\"next\"><a href=\"$target?page=$next&" . buildURL() . "\"> Next</a></li>\n";
		}
		if ($page < $pages - 2) {
			$paginationDisplay .=  "\t\t\t<li class=\"last\"><a href=\"$target?page=$pages&" . buildURL() . "\"> Last</a></li>\n";
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Display Cardio Exercises</title>
		<?php
			loadJavascript("../scripts/loader.js");
			loadJQuery();
		?>
		<script>
			loadCSS("../css/list.css");

			var URIEncode = function(str) {
				return str.replace(/ /g, '+');
			};
			
			var buildURL = function(eq, st, en) {
			
				var url = "<?php echo $target ?>";
				
				var arr = new Array();
				var res = ((eq === '0' && st === '' && en === '') ? '' : '?');
				
				if(eq !== '0') arr.push('equip=' + URIEncode(eq));
				if(st !== '') arr.push('sdate=' + URIEncode(st));
				if(en !== '') arr.push('edate=' + URIEncode(en));
				
				return (url + ((res === '') ? '' : res + arr.join('&')));
			};
			
			$(document).ready(function(){
				var mind = new Date(<?php echo $mindate->format('Y'); ?>, <?php echo $mindate->format('m'); ?> - 1, <?php echo $mindate->format('d'); ?>);
				var maxd = new Date(<?php echo $maxdate->format('Y'); ?>, <?php echo $maxdate->format('m'); ?> - 1, <?php echo $maxdate->format('d'); ?>);
			
				var equip = '<?php echo (isset($_GET['equip']) ? $_GET['equip'] : '0'); ?>';
				var sdate = '<?php echo (isset($_GET['sdate']) ? $_GET['sdate'] : ''); ?>';
				var edate = '<?php echo (isset($_GET['edate']) ? $_GET['edate'] : ''); ?>';
				
				$('#equip').val(equip);
				$('#equip').change(function() {
					equip = $('#equip').val();
					$('#filter').prop("href", buildURL(equip, sdate, edate));
				});
				$('#sdate').datepicker({
					changeMonth:true,
					changeYear:true,
					yearRange: "-100:+0",
					minDate: mind,
					maxDate: maxd,
					dateFormat: 'dd MM yy',
				});
				$('#sdate').change(function() {
					sdate = $('#sdate').val();
					$('#filter').prop("href", buildURL(equip, sdate, edate));
				});
				$('#edate').datepicker({
					changeMonth:true,
					changeYear:true,
					yearRange: "-100:+0",
					minDate: mind,
					maxDate: maxd,
					dateFormat: 'dd MM yy',
				});
				$('#edate').change(function() {
					edate = $('#edate').val();
					$('#filter').prop("href", buildURL(equip, sdate, edate));
				});
			});
		</script>
	</head>
	<body>
<?php
	echo "\t\t<div id=\"lhead\">\n";
	echo "\t\t\t<h1 id=\"ltitle\">List of Cardio Exercises For {$details->fullname()}</h1>\n";
	if (isset($_GET['action'])) {
		echo "<p class=\"action\">{$_GET['action']}</p>\n";
	} else {
		echo "<p class=\"action\"></p>\n";
	}
?>
			<p id="totrecs">Displaying Records <?php echo $start + 1; ?> to <?php echo $start + $numrecs; ?> out of a total of <?php echo $totrecs; ?> Results</p>
		</div>
		<div class="formcon">
		<input type="hidden" name="page" value="1">
		<select name="equip" id="equip">
			<?php foreach ($all_equipment as $opt) { ?>
				<option value="<?php echo $opt->getID(); ?>"><?php echo $opt->getName(); ?></option>
			<?php } ?>
		</select>
		<p class="dpicker">
			<label for="sdate">Start Date</label>
			<input type="text" id="sdate" name="sdate" value="<?php echo (isset($_GET['sdate']) ? $_GET['sdate'] : ""); ?>">
		</p>
		<p class="dpicker">
			<label for="edate">End Date</label>
			<input type="text" id="edate" name="edate" value="<?php echo (isset($_GET['edate']) ? $_GET['edate'] : ""); ?>">
		</p>
		<p>
			<a id="filter" href="<?php echo $baseurl; ?>">Filter</a>
		</p>
		<p>
			<a href="<?php echo $target; ?>">Display All Data</a>
		</p>
		</div>
		<div id="pagcon">
		<ul id="pagination">
			<?php echo "$paginationDisplay\n"; ?>
		</ul>
		</div>
		<div id="data">
			<table>
				<tr class="dhead">
					<th class="hodd">Observation Date</th>
					<th class="heven">Venue</th>
					<th class="hodd">Equipment</th>
					<th class="heven">Level</th>
					<th class="hodd">Program</th>
					<th class="heven">Minutes</th>
					<th class="hodd">Speed</th>
					<th class="heven">Incline</th>
					<th class="hodd">Distance</th>
					<th class="heven">Calories</th>
					<th class="hedit"></th>
					<th class="hdelete"></th>
				</tr>
<?php
	foreach ($objects as $object) {
		$venue = tblvenues::get($object->getVenueID());
		$equipment = tblcardioequipment::get($object->getEquipmentID());
		$level = ($object->getLevel() != 0 ? $object->getLevel() : "");
		$program = tblcardioprogs::get($object->getProgID())->getProgram();
		$speed = ($object->getSpeed() != 0 ? $object->getSpeed() : "");
		$incline = ($object->getIncline() != 0 ? $object->getIncline() : "");
		$distance = ($object->getDistance() != 0 ? $object->getDistance() : "");
		$calories = ($object->getCalories() != 0 ? $object->getCalories() : "");
		echo "\t\t\t\t<tr class=\"drow\">\n";
		echo "\t\t\t\t\t<td class=\"dodd\"><strong>{$object->getObsDate()->format('l jS F Y')}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven venue\"><strong>{$venue->getVenue()}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dodd equipment\"><strong>{$equipment->getName()}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven level\"><strong>{$level}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dodd program\"><strong>{$program}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven minutes\"><strong>{$object->getMinutes()}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dodd speed\"><strong>{$speed}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven incline\"><strong>{$incline}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dodd distance\"><strong>" . number_format((float)$distance, ($equipment->getKM() == 1 ? 2 : 0)) . "</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven calories\"><strong>{$calories}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dedit\"><a href=\"cardioform.php?id={$object->getid()}\"><img class=\"dicon\" src=\"../images/edit.png\" alt=\"Edit\"> Edit</a></td>\n";
		echo "\t\t\t\t\t<td class=\"ddelete\"><a href=\"delete_cardio.php?id={$object->getid()}\" onclick=\"return confirm('Do You Really Want To Delete This Record?')\"><img class=\"dicon\" src=\"../images/delete.png\" alt=\"Edit\"> Delete</a></td>\n";
		echo "\t\t\t\t</tr>\n";
	}
?>
			<tr><td colspan="12" class="daddrec"><a href="cardioform.php">Add New Record</a></td></tr>
			</table>
		</div>
		<div id="footer">
			<a href="weekly_calories.php" class="button">Weekly Calories</a>
			<a href="../index.php" class="button">Back To Dashboard</a>
			<a class="button" href="logout.php">Log Out</a>
		</div>
	</body>
</html>
<?php
	function buildURL() {
		$url = array();
		if (isset($_GET['equip']) && ($_GET['equip'] != "0")) $url[] = "equip=" . urlencode($_GET['equip']);
		if (isset($_GET['sdate']) && ($_GET['sdate'] != "")) $url[] = "sdate=" . urlencode($_GET['sdate']);
		if (isset($_GET['edate']) && ($_GET['edate'] != "")) $url[] = "edate=" . urlencode($_GET['edate']);
		if (empty($url)) return null;
		return implode($url, '&');
	}
?>
