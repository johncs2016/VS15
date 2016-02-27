<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" >
		<title>Display Of Users</title>
		<link rel="stylesheet" type="text/css" href="../css/pagination.css">
		<link rel="stylesheet" type="text/css" href="../css/website.css">
	</head>
	<body>
<?php
	include "user.php";
	
	echo "<h2>List of Users</h2>";
	if (isset($_GET['action'])) echo '<p>' . $_GET['action'] . '</p>';
	$target = $_SERVER['PHP_SELF'];
	$limit = 10;
	$totrecs = tblusers::getRecordCount();
	$pages = ceil($totrecs / $limit);
	$page = (isset($_GET['page']) ? preg_replace('#[^0-9]#i', '', $_GET['page']) : 1);
	$page = ($page < 1) ? 1 : (($page > $pages) ? $pages : $page);
	$objects = ($totrecs < $limit) ? tblusers::getAll() : tblusers::getAll(($page - 1) * $limit, $limit);
	$fields = tblusers::getFields();
	$centrePage = '';
	$prev = $page - 1;
	$prev2 = $prev - 1;
	$next = $page + 1;
	$next2 = $next + 1;
	if ($page == 1) {
    	$centrePage .= '&nbsp; <span class="pagNumActive">' . $page . '</span> &nbsp;';
    	$centrePage .= '&nbsp; <a href="' . $target . '?page=' . $next . '">' . $next . '</a> &nbsp;';
	}
	else if ($page == $pages) {
    	$centrePage .= '&nbsp; <a href="' . $target . '?page=' . $prev . '">' . $prev . '</a> &nbsp;';
    	$centrePage .= '&nbsp; <span class="pagNumActive">' . $page . '</span> &nbsp;';
	}
	else if ($page > 2 && $page < ($pages - 1)) {
    	$centrePage .= '&nbsp; <a href="' . $target . '?page=' . $prev2 . '">' . $prev2 . '</a> &nbsp;';
    	$centrePage .= '&nbsp; <a href="' . $target . '?page=' . $prev . '">' . $prev . '</a> &nbsp;';
    	$centrePage .= '&nbsp; <span class="pagNumActive">' . $page . '</span> &nbsp;';
    	$centrePage .= '&nbsp; <a href="' . $target . '?page=' . $next . '">' . $next . '</a> &nbsp;';
    	$centrePage .= '&nbsp; <a href="' . $target . '?page=' . $next2 . '">' . $next2 . '</a> &nbsp;';
	}
	else if ($page > 1 && $page < $pages) {
    	$centrePage .= '&nbsp; <a href="' . $target . '?page=' . $prev . '">' . $prev . '</a> &nbsp;';
    	$centrePage .= '&nbsp; <span class="pagNumActive">' . $page . '</span> &nbsp;';
    	$centrePage .= '&nbsp; <a href="' . $target . '?page=' . $next . '">' . $next . '</a> &nbsp;';
	}
	$paginationDisplay = ""; // Initialize the pagination output variable

	if ($pages != 1){
	    $paginationDisplay .= 'Page <strong>' . $page . '</strong> of ' . $pages. '&nbsp;  &nbsp;  &nbsp; ';
	    if ($page != 1) {
         	$paginationDisplay .=  '&nbsp;  <a href="' . $target . '?page=' . $prev . '"> Back</a> ';
    	}
	    $paginationDisplay .= '<span class="paginationNumbers">' . $centrePage . '</span>';
	    if ($page != $pages) {
        	$paginationDisplay .=  '&nbsp;  <a href="' . $target . '?page=' . $next . '"> Next</a> ';
    	} 
	}
?>
   <div style="margin-left:64px; margin-right:64px;">
     <h2>Total Items: <?php echo $totrecs; ?></h2>
   </div> 
     <div style="margin-left:58px; margin-right:58px; padding:6px; background-color:#FFF; border:#999 1px solid;"><?php echo $paginationDisplay; ?></div>
<?php
	echo "<table border='1'>";
	echo '<tr>';
	foreach ($fields as $value) {
		echo "<th align='center'>" . $value . '</th>';
	}
	echo '<th></th><th></th></tr>';
	foreach ($objects as $object) {
		echo '<tr>';
		$class = get_class($object);
		$reflect = new ReflectionClass($class);
		foreach ($fields as $field) {
			echo "<td align='center'><strong>";
			$property = '_'.$field;
			$prop = $reflect->getProperty($property);
			$prop->setAccessible(true);
			$value = $prop->getValue($object);
			$type = gettype($value);
			if ($type == 'object') {
				$type = get_class($value);
			}
			$print_val = '';
			switch ($type) {
				case 'DateTime':
					$print_val = $value->format('j M Y');
					break;
				case 'float':
					$print_val = number_format($value,1);
					break;
				case 'double':
					$print_val = number_format($value,1);
					break;
				default:
					$print_val = $value;
					break;
			}
			echo $print_val;
			echo "</strong></td>";
		}
		echo '<td><a href="userform.php?id='.$object->getid().'">Edit Record</a></td><td><a href="delete_user.php?id='.$object->getid().'" onclick="return confirm(\'Do You Really Want To Delete This Record?\')">Delete Record</a></td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '<a href="userform.php">Add New Record</a>';
?>
</body>
</html>