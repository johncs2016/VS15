<?php
			require_once("config.php");

			//Connecting to your database
			$dsn = 'mysql:host='.$config['db']['hostname'].';dbname='.$config['db']['dbname'];
			$db = new PDO($dsn, $config['db']['username'], $config['db']['password']);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
			//Fetching from your database table.
			$sql = 'SELECT ' . implode(', ', $config['db']['Fields']) . ' FROM ' . $config['db']['usertable'];
			try {
				echo "<h2>List of Results from HTML5 Canvas</h2>";
				echo "<table border='1'>";
				$query = $db->query($sql);
				$root = $query->fetchall(PDO::FETCH_ASSOC);
				// This prints out the required HTML markup
				echo '<tr>';
				foreach ($root[0] as $key => $value) {
					echo "<th align='center'>" . $key . '</th>';
				}
				echo '</tr>';
				foreach ($root as $rec) {
					echo '<tr>';
					foreach ($rec as $key => $value) {
						echo "<td align='center'>" . (($key == 'ObservationDate') ? date('j M', strtotime($value)) : number_format((float)$value,1)) . "</td>";
					}
					echo '</tr>';
				}
				echo '</table>';
				echo '<p><strong>Returned ' . $query->rowcount() . ' results.</strong></p>';
				}
			catch (PDOException $e) {
				echo $e->getMessage();
			}
?>
