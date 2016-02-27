window.onload = function (e)
{
	var ydata = new Array(
		<?php echo $ymin; ?>,
		<?php echo $ymax; ?>,
		<?php echo $weight_string; ?>,
		"<?php echo $data_const["canvas"][1]; ?>",
		"<?php echo $data_const["description"][1]; ?>",
		"<?php echo $data_const["units"][1]; ?>"
	);
	var zdata = new Array(
		<?php echo $zmin; ?>,
		<?php echo $zmax; ?>,
		<?php echo $waist_string; ?>,
		"<?php echo $data_const["canvas"][2]; ?>",
		"<?php echo $data_const["description"][2]; ?>",
		"<?php echo $data_const["units"][2]; ?>"
	);
	draw_canvas(ydata);
	draw_canvas(zdata);
}

function draw_canvas(ydata) {
	var ymin = ydata[0];
	var ymax = ydata[1];
	var yscale = ymax - ymin;
	var data = ydata[2];
	var cat = <?php echo $cat_string ?>;
	var grid_marks = <?php echo $grid_marks; ?>;
	var canvasID = 's' + ydata[3];
        var imageID = 'p' + ydata[3];
	var col = (canvasID == 'scvsWeight' || canvasID == 'pcvsWeight' ? 'red' : 'blue');
	var description = ydata[4];
	var units = ydata[5];
	var line = new RGraph.Line(canvasID, data);
	line.Set("chart.background.grid", true);
	line.Set("chart.background.grid.color", "purple");
	line.Set("chart.tickmarks", "filledcircle");
	line.Set("chart.line.colors", [col]);
	line.Set("chart.linewidth", 2);
	line.Set("chart.title", "Record of " + description + " Over Time" );
	line.Set("chart.title.color", "cyan");
	line.Set("chart.title.bold", "true");
	line.Set("chart.title.background", "violet");
	line.Set("Chart.title.size", 16);
	line.Set('chart.colors', [col]);
	line.Set("chart.labels", cat);
	line.Set("chart.ylabels", true);
	line.Set("chart.noaxes", true);
	line.Set("chart.ymin", ymin);
	line.Set("chart.ymax", ymax);
	line.Set("chart.scale.decimals", 1);
	line.Set("chart.numyticks", yscale);
	line.Set("chart.background.grid.autofit.numvlines", grid_marks);
	line.Set("chart.background.grid.autofit.numhlines", 10 * yscale);
	line.Set("chart.ylabels.count", 5 * yscale);
	line.Set("chart.ylabels.color", "maroon");
	line.Set("chart.text.angle", 0);
	line.Set("chart.gutter.left", 50);
	line.Set("chart.gutter.bottom", 50);
	line.Set("chart.gutter.top", 45);
	line.Set("chart.title.xaxis", "Date");
	line.Set("chart.title.xaxis.size", 11);
	line.Set("chart.title.xaxis.bold", true);
	line.Set("chart.title.yaxis.size", 11);
	line.Set("chart.text.color", "maroon");
	line.Set("chart.text.bold", true);
	line.Set("chart.text.size", 10);
	line.Set("chart.title.xaxis.pos", 0.25);
	line.Set("chart.title.yaxis", description + " (" + units + ")");
	line.Set("chart.title.yaxis.color", "maroon");
	line.Set("chart.title.yaxis.pos", 0.25);

    	var ca = line.canvas;
	var chartarea = new RGraph.Drawing.Rect(canvasID,0,0,ca.width,ca.height);
	chartarea.Set('chart.strokestyle', 'rgb(255,255,0)');
	chartarea.Set('chart.fillstyle', 'rgb(255,255,0)');
	chartarea.Draw();

	var plotarea = new RGraph.Drawing.Rect(canvasID,line.Get('chart.gutter.left'),line.Get('chart.gutter.top'),ca.width - line.Get('chart.gutter.right') - line.Get('chart.gutter.left'),ca.height - line.Get('chart.gutter.top') - line.Get('chart.gutter.bottom'));
	plotarea.Set('chart.strokestyle', 'rgb(255,215,0)');
	plotarea.Set('chart.fillstyle', 'rgb(255,215,0)');
	plotarea.Draw();
	
	line.Draw();
	var image_data = line.canvas.toDataURL("image/jpeg");
	document.getElementById(imageID).src = image_data;
	return;
}

