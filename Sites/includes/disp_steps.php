<?php
	include_once("sessions.php");
?>
<!doctype html>
<html>
<head>
<title>Steps Counts</title>
<style>
	.amiddle {
		text-align: center;
	}

	.dbutton {
		width:180px;
		height:60px;
		vertical-align: middle;
	}
</style>
</head>
<body>
<?php
	require_once "steps.php";
	require_once "user.php";
	require_once "activities.php";
	require_once "composition.php";
	include "display_functions.php";

	$target = htmlentities($_SERVER['PHP_SELF']);
	if (isset($_GET['action'])) echo '<p>' . $_GET['action'] . '</p>';
	$page = (isset($_GET['page']) ? preg_replace('#[^0-9]#i', '', $_GET['page']) : 1);
	$per_page = 20;
	$num_rows = tblstepscounts::getRecordCount();
	$num_pages = ceil($num_rows / $per_page);
	if($page >= 1 && $page <= $num_pages) {
		$start = ($page - 1) * $per_page;
		$user = tblusers::getAll(1)[0];

		echo "<table>\n\t<thead>\n\t\t<tr><th>Date</th><th>Number Of Steps Walked</th><th>Distance Walked</th><th>Activity</th><th></th><th></th></tr>\n\t</thead>\n\t<tbody>\n";

		$objects = tblstepscounts::getAll($start, $per_page);
		foreach($objects as $object) {
			$id = $object->getID();
			echo "\t\t<tr><td>" . $object->getObsDate()->format('l jS F Y') . "</td>";
			echo "<td class=\"amiddle\">" . $object->getStepsCount() . "</td>";
			echo "<td>" . display::display_value(composition::distance_walked($user->getStepSize(), $object->getStepsCount()), 3, $user->getIsImperial()) . "</td>";
			echo "<td>" . tblactivities::get($object->getActivityID())->getactivity() . "</td>";
			echo "<td><a href=\"stepsform.php?id=$id\">Edit</a></td>";
			echo "<td><a href=\"deletesteps.php?id=$id\" onclick=\"return confirm('Do You Really Want To Delete This Record?')\">Delete</a></td></tr>\n";
		}
		echo "<tr><td colspan=\"6\" class=\"amiddle\"><a href=\"stepsform.php\">Add New Record</a></td></tr>\n";

		echo "\t</tbody>\n</table>\n<br />";

		if($num_pages > 1) {
			for($p = 1; $p <= $num_pages; $p++) {
				echo "<a href=\"$target?page=$p\">$p</a>" . htmlentities("     ");
			}
		}
	}
?>
<br /><a href="dashboard.php"><img class="dbutton"src="../images/back_button.gif" alt="Back To Dashboard"></a>
</body>
</html>

