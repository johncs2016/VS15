<?php
			require_once("configWeight.php");

			//Connecting to your database
			$dsn = 'mysql:host='.$config['db']['hostname'].';dbname='.$config['db']['dbname'];
			$db = new PDO($dsn, $config['db']['username'], $config['db']['password']);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
			//Fetching from your database table.
			$sql = 'SELECT ' . implode(', ', $config['db']['Fields']) . ' FROM ' . $config['db']['usertable'];
			try {
				$query = $db->query($sql);
				$root = $query->fetchall(PDO::FETCH_ASSOC);
				// This prints out the required HTML markup
				$category = array();
				$values = array();
				$i = 0;
				foreach ($root as $key => $rec) {
					$category[] = "'" . date('j M', strtotime($rec[$config['db']['Fields']['X']])) . "'";
					$values[] = number_format((float)$rec[$config['db']['Fields']['Y']],1);
					$f_values[] = (float)$values[$i];
					$i++;
				}
				$ymin = 2 * floor(min($f_values) / 2);
				$ymax = 2 * ceil(max($f_values) / 2);
				$data_string = '[' . implode(',',$values) . ']';
				$cat_string = '[' . implode(',',$category) . ']';
				$grid_marks = count($root) - 1; ?>
				<script src="../Libraries/RGraph.common.core.js"></script>
				<script src="../Libraries/RGraph.line.js"></script>
				<script src="../Libraries/RGraph.drawing.rect.js"></script>
				<canvas id="<?php echo $config['db']['canvasID']; ?>" width="600" height="400"></canvas>
				<script>
					var ymin = <?php echo $ymin ?>;
					var ymax = <?php echo $ymax ?>;
					var yscale = (ymax - ymin) / 2;
					var data = <?php echo $data_string ?>;
					var cat = <?php echo $cat_string ?>;
					var grid_marks = data.length - 1;
				
					var line = new RGraph.Line("<?php echo $config['db']['canvasID']; ?>", data);
					line.Set("chart.linewidth", 2);
					line.Set("chart.title", "Record of <?php echo $config['db']['descrip'];?> Over Time" );
					line.Set("chart.title.color", "cyan");
					line.Set("chart.title.background", "green");
					line.Set("chart.labels", cat);
					line.Set("chart.ylabels", false);
					line.Set("chart.noaxes", true);
					line.Set("chart.ymin", ymin);
					line.Set("chart.ymax", ymax);
					line.Set("chart.scale.decimals", 0);
					line.Set("chart.numyticks", yscale);
					line.Set("chart.background.grid.autofit.numvlines", grid_marks);
					line.Set("chart.background.grid.autofit.numhlines", yscale);
					line.Set("chart.ylabels.count", 10);
					line.Set("chart.ylabels.color", "blue");
					line.Set("chart.text.angle", 90);
					line.Set("chart.gutter.left", 75);
					line.Set("chart.gutter.bottom", 100);
					line.Set("chart.gutter.top", 50);
					line.Set("chart.title.xaxis", "Date");
					line.Set("chart.text.color", "maroon");
					line.Set("chart.title.xaxis.pos", 0.25);
					line.Set("chart.title.yaxis", "<?php echo $config['db']['descrip'];?> (<?php echo $config['db']['units'];?>)");
					line.Set("chart.title.yaxis.color", "maroon");
					line.Set("chart.title.yaxis.pos", 0.25);
		        	line.ondraw = function (obj) {
		            	var ca = obj.canvas;
        		    	var co = obj.context;
        		    	var ht = (ca.height - obj.Get('chart.gutter.bottom') - obj.Get('chart.gutter.top'));
 
            			for (var i=0; i<=yscale; i++) {
                
                			var x     = obj.Get('chart.gutter.left') - 5 * ((ymin + 2 * i)+'').length - 10;
                			var y     = ca.height - obj.Get('chart.gutter.bottom') - i * ht / yscale + 5;
                 			var font  = 'Arial';
                			var size  = 8;
                			var text  = ymin + 2 * i;
                 			RGraph.Text(co, font, size, x, y, text);
			            }
            
             			// Because we're not drawing an axis, draw an extra grid line
			            co.beginPath();
            			co.strokeStyle = obj.Get('black');
                		co.moveTo(obj.Get('chart.gutter.left'), ca.height - obj.Get('chart.gutter.bottom'));
                		co.lineTo(ca.width - obj.Get('chart.gutter.right'), obj.canvas.height - obj.Get('chart.gutter.bottom'));
                		co.lineTo(ca.width - obj.Get('chart.gutter.right'), obj.Get('chart.gutter.top'));
            			co.lineTo(obj.Get('chart.gutter.left'), obj.Get('chart.gutter.top'));
                		co.lineTo(obj.Get('chart.gutter.left'), ca.height - obj.Get('chart.gutter.bottom'));
            			co.stroke();
             			var rect = new RGraph.Drawing.Rect("<?php echo $config['db']['canvasID']; ?>",
             				obj.Get('chart.gutter.left'), obj.Get('chart.gutter.top'),
             				ca.width - obj.Get('chart.gutter.right') - obj.Get('chart.gutter.left'),
             				ca.height - obj.Get('chart.gutter.top') - obj.Get('chart.gutter.bottom'));
	             		rect.strokeStyle = obj.Get('transparent');
               			rect.Set('chart.fillstyle', 'rgba(255,0,0,0.2)');
                		rect.Draw();
	    		    }
				
					line.Draw();
				</script>
				
<?php
}
			catch (PDOException $e) {
				echo $e->getMessage();
			}
