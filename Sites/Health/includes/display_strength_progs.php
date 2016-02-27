<?php
	include_once("sessions.php");
	include "strength.php";
	include "strengthequipment.php";
	include "venues.php";
	include("validator.php");
	require_once("load_libraries.php");

	$venues = tblvenues::getAll();
	$userid = $_SESSION['userid'];	
	$action = (empty($_POST) ? '' : 'New Records have been added');
	$now = new DateTime();
	$user = tblusers::get($_SESSION["userid"]);
	$details = tbldetails::get($user->getDetailsID());
	$target = $_SERVER['PHP_SELF'];
	$limit = 20;
	$totrecs = tblstrengthprogs::getRecordCount();
	$pages = ceil($totrecs / $limit);
	$page = (isset($_GET['page']) ? preg_replace('#[^0-9]#i', '', $_GET['page']) : 1);
	$page = ($page < 1) ? 1 : (($page > $pages) ? $pages : $page);
	$start = ($page - 1) * $limit;
	$objects = ($totrecs < $limit) ? tblstrengthprogs::getAll(0 , $totrecs) : tblstrengthprogs::getAll($start, $limit);
	if(!empty($_POST)) {
		$input = array(
			'odate'		=>	DateTime::CreateFromFormat("d F Y", $_POST['sdate']),
			'venueid'	=>	$_POST['venue']
		);
		
		try {
			$properties = array(
				'_userid'		=>	$userid,
				'_obsdate'		=>	$input['odate']->format('d/m/Y'),
				'_venueid'		=>	$_POST['venue']
			);
			
			$val_object = new validator();
			$valid = $val_object->validate_date("obsdate", $properties["_obsdate"]);
			$error_array = $val_object->getAllErrors();
			
			$venue = tblvenues::get($properties['_venueid']);
			if ($venue == null) {
				if (isset($error_array['venue'])) $error_array['venue'] .= "\n"; else $error_array['venue'] = '';
				$error_array['venue'] .= "Venue Does Not Exist";
				$valid = false;
			}
			
			if ($valid) {
				$default_venue = $venue;
				$recs_by_date = tblstrength::getByDate($input['odate']);
				if(empty($recs_by_date)) {
					foreach ($objects as $obj) {
						$properties['_equipid'] = $obj->getEquipmentID();
						$properties['_weight'] = $obj->getWeight();
						$properties['_sets'] = $obj->getSets();
						$properties['_reps'] = $obj->getReps();
						$object = tblstrength::create($properties);
					}
				} else {
					$action = "Records have already been entered";
				}
			}
		}
		catch (PDOException $e) {
			$action = 'ERROR: records cannot be added';
		}
	} else {
		$default_venue = $venues[0];
	}
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
	    	$centrePage .= "\t\t\t\t\t<li><a href=\"$target?page=$prev\">$prev</a></li>\n";
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
		<title>Display Strength Program</title>
		<?php
			loadJavascript("../scripts/loader.js");
			loadJQuery();
		?>
		<script>
			loadCSS("../css/list.css");

			var URIEncode = function(str) {
				return str.replace(/ /g, '+');
			};
			
			$(document).ready(function(){
				var maxd = new Date();
				$('#sdate').datepicker({
					changeMonth:true,
					changeYear:true,
					yearRange: "-100:+0",
					maxDate: maxd,
					dateFormat: 'dd MM yy',
				});
			});
		</script>
	</head>
	<body>
<?php
	echo "\t\t<div id=\"lhead\">\n";
	echo "\t\t\t<h1 id=\"ltitle\">Strength Programme For {$details->fullname()}</h1>\n";
	echo "<p class=\"action\">$action</p>\n";
?>
			<p id="totrecs">Displaying Records <?php echo $start + 1; ?> to <?php echo $start + $numrecs; ?> out of a total of <?php echo $totrecs; ?> Results</p>
		</div>
		<div class="formcon">
			<form id="dataform" class="data_form" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">
				<input type="hidden" name="page" value="1">
				<p class="dpicker">
					<label for="sdate">Observation Date</label>
					<input type="text" id="sdate" name="sdate" value="<?php echo $now->format('d F Y'); ?>">
				</p>
				<p class="tselect">
					<label for="venue">Venue</label>
					<select name="venue" id="venue">
						<?php
							foreach ($venues as $object) {
								$id = $object->getID();
								echo '<option ' . ($id == $default_venue->getID() ? 'selected ' : '') . 'value="'.$id.'">' . $object->getVenue() . '</option>';
							}
						?>
					</select>
					<span class="form_hint">* Please Select an venue from the drop-down list</span>
				</p>
			</form>
		</div>
		<div id="pagcon">
		<ul id="pagination">
			<?php echo "$paginationDisplay\n"; ?>
		</ul>
		</div>
		<div id="data">
			<table>
				<tr class="dhead">
					<th class="hodd">Equipment</th>
					<th class="heven">Weight</th>
					<th class="hodd">Sets</th>
					<th class="heven">Reps</th>
					<th class="hedit"></th>
					<th class="hdelete"></th>
				</tr>
<?php
	foreach ($objects as $object) {
		$equipment = tblstrengthequipment::get($object->getEquipmentID());
		$eweight = ($object->getWeight() != 0 ? $object->getWeight() : "");
		$sets = ($object->getSets() != 0 ? $object->getSets() : "");
		$reps = ($object->getReps() != 0 ? $object->getReps() : "");
		echo "\t\t\t\t<tr class=\"drow\">\n";
		echo "\t\t\t\t\t<td class=\"dodd equipment\"><strong>{$equipment->getName()}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven level\"><strong>{$eweight}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dodd program\"><strong>{$sets}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"deven minutes\"><strong>{$reps}</strong></td>\n";
		echo "\t\t\t\t\t<td class=\"dedit\"><a href=\"strengthprogsform.php?id={$object->getid()}\"><img class=\"dicon\" src=\"../images/edit.png\" alt=\"Edit\"> Edit</a></td>\n";
		echo "\t\t\t\t\t<td class=\"ddelete\"><a href=\"delete_strength_progs.php?id={$object->getid()}\" onclick=\"return confirm('Do You Really Want To Delete This Record?')\"><img class=\"dicon\" src=\"../images/delete.png\" alt=\"Edit\"> Delete</a></td>\n";
		echo "\t\t\t\t</tr>\n";
	}
?>
			<tr><td colspan="12" class="daddrec"><a href="strengthprogsform.php">Add New Record</a></td></tr>
			</table>
		</div>
		<div id="footer">
			<a href="../index.php" class="button">Back To Dashboard</a>
			<a href="display_strength.php" class="button">Back To Results</a>
			<button type="submit" id="submit" class="button" form="dataform">Submit To Database</button>
			<a class="button" href="logout.php">Log Out</a>
		</div>
	</body>
</html>
