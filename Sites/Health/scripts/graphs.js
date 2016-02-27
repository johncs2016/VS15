require("loader.js");
loadJQuery();
require("math.js");
require("display_values.js");

function setIDX(flag) {
	return (flag ? 'x' : 'y');
}

function loadGraph(cats, data, isWeight, isimp) {
	var units = {
			'weight'   : ['kg', 'stone'],
			'size'     : ['cm', 'in'],
			'height'   : ['m', 'ft'],
			'distance' : ['km', 'mile']
	};

	var getUnits = function(units, isimp, isWeight) {
		var i = (isimp ? 1 : 0);
		var m = (isWeight ? 'weight' : 'size');
		return units[m][i];
	};

	var getTitle = function(isWeight) {
		return (isWeight ? "Weight" : "Waist Size");
	};

	var getChartTitle = function(isWeight) {
		return ("Record of " + getTitle(isWeight) + "s");
	};

	var getYTitle = function(units, isimp, isWeight) {
		return getTitle(isWeight);
	};

	var options = {
		'isimp'    : isimp,
		'isWeight' : isWeight,
		'ccolor'   : "rgb(255,255,0)",
		'lwidth'   : 2,
		'pcolor'   : "gold",
		'gcolor'   : "purple",
		'tcolor'   : "cyan",
		'acolor'   : "maroon",
		'dcolor'   : (isWeight ? "red" : "blue"),
		'title'    : getChartTitle(isWeight),
		'XTitle'   : "Date",
		'YTitle'   : getYTitle(units, isimp, isWeight),
		'cats'     : cats,
		'data'     : data
	};
	var g = new Graph("s" + (isWeight ? "cvsWeight" : "cvsWaist"), options);
	g.draw();
	g.save();
}

extendClass = {};

extendClass.extend = function(subClass, baseClass) {
	function inheritance() {};
	inheritance.prototype = baseClass.prototype;
	subClass.prototype = new inheritance();
	subClass.prototype.constructor = subClass;
	subClass.baseConstructor = baseClass;
	subClass.superClass = baseClass.prototype;
}

function Graph(canvasID, options) {
	this.printID = 'p' + canvasID.substr(1);
	this.canvas = document.getElementById(canvasID);
	this.context = this.canvas.getContext("2d");
	this.createChartTitle(options['title'], options['tcolor']);
	this.createChartArea(options['ccolor']);
	this.createPlotArea(options['pcolor']);
	this.createDataSeries(options['cats'], options['data'], options['isWeight'], options['isimp']);
	this.createDataPoints(options['dcolor']);
	this.createDataLine(options['dcolor'], options['lwidth']);
	this.createAxes(options['XTitle'], options['YTitle'], options['acolor'])
	this.createGrid(options['gcolor']);
}

Graph.prototype.setSeries = function(series, data, isWeight, isimp, flag) {
	var idx = setIDX(flag);
	series[idx].data = data;
	series[idx].nticks = (isimp && isWeight && !flag ? 14 : 10);
	if(!flag) {
		series[idx].isimp = isimp;
		series[idx].isWeight = isWeight;
		series[idx].impData = [];
		for (var i = 0; i < data.length; i++) {
			series[idx].impData[i] = (isWeight ? STONEfromKG(series[idx].data[i]) : INfromCM(series[idx].data[i]));
		}
	}
	var vals = ((isimp && !flag) ? series[idx].impData : data);
	series[idx].minValue = Math.floor(Math.min.apply(Math, vals));
	series[idx].maxValue = Math.ceil(Math.max.apply(Math, vals));
	series[idx].scale = (series[idx].maxValue - series[idx].minValue) / (flag ? series[idx].nticks : 1);
	series[idx].nticks *= series[idx].scale;
	series[idx].intval = (series[idx].nticks < 1 ? 0 : (flag ? this.PlotArea.width : this.PlotArea.height) / series[idx].nticks);
}

Graph.prototype.createDataSeries = function(cats, data, isWeight, isimp) {
	var series = [];
	series['x'] = [];
	series['y'] = [];
	cat_vals = [];
	objs = [];
	for (var i = 0; i < cats.length; i++) {
		objs[i] = new Date();
		objs[i].setTime(1000 * Number(cats[i]));
		objs[i].setHours(0);
		
		cat_vals[i] = Math.ceil(objs[i].valueOf() / (60 * 60 * 24 * 1000));
	}
	var min_val = Math.min.apply(Math, cat_vals);
	series['x'].minDate = zerofill(Number(objs[0].getFullYear()), 4) + '-' + zerofill(Number(objs[0].getMonth()) + 1, 2) + '-' + zerofill(Number(objs[0].getDate()), 2);
	for (var i = 0; i < cat_vals.length; i++) {
		cat_vals[i] -= min_val;
	}
	this.setSeries(series, cat_vals, isWeight, isimp, true);
	this.setSeries(series, data, isWeight, isimp, false);
	this.series = series;
}

Graph.prototype.getDataPoints = function() {
	var pt = this.PlotArea.getPoint(1);
	var dp = [];
	var dates = this.series['x'].data;
	var vals = (this.series['y'].isimp ? this.series['y'].impData : this.series['y'].data);
	
	for(var i in this.series['y'].data) {
		var x = pt.x + this.PlotArea.width * (dates[i] - this.series['x'].minValue) / this.series['x'].nticks;
		var y = pt.y - this.PlotArea.height * (vals[i] - this.series['y'].minValue) / this.series['y'].scale;
		dp[i] = new Point(x, y);
	};
	return dp;
}

Graph.prototype.createDataPoints = function(clr) {
	var dp = this.getDataPoints();
	var obj = [];
	for (var i in dp) {
		obj[i] = new Circle(this.context, {'p1' : dp[i], 'radius' : 5, 'colour' : clr});
	}
	this.DataPoints = obj;
}

Graph.prototype.createDataLine = function(clr, lwidth) {
	var dp = this.getDataPoints();
	var arr = dp;
	var p1 = arr.shift();
	this.dataLine = new Line(this.context, {'p1' : p1, 'p2' : arr, 'colour' : clr, 'stroke' : true, 'lwidth' : lwidth});
}

Graph.prototype.createChartArea = function(clr) {
	var p0 = new Point();
	this.ChartArea = new Rect(this.context, {'p1' : p0, 'width' : this.canvas.width, 'height' : this.canvas.height, 'colour' : clr});
}

Graph.prototype.createPlotArea = function(clr) {
	var p0 = new Point(0.125 * this.canvas.width, 0.15 * this.canvas.height);
	this.PlotArea = new Rect(this.context, {'p1' : p0, 'width' : 0.85 * this.canvas.width, 'height' : 0.75 * this.canvas.height, 'colour' : clr});
}

Graph.prototype.createLabels = function(flag) {
	var labs = [];
	var val;
	if(!flag) {
		var isimp = this.series['y'].isimp;
		var isWeight = this.series['y'].isWeight;
	}
	var nx = this.series['x'].nticks;
	var ny = this.series['y'].nticks / this.series['y'].scale;
	for (var i = 0; i <= (flag ? nx : ny); i++) {
		if(flag) {
			var dte = new Date(this.series['x'].minDate);
			dte.setHours(0);
			var inc = this.series['x'].minValue + i;
			dte.setDate(dte.getDate() + inc);
			labs[i] = dte.getDate() + '/' + (dte.getMonth() + 1);
		} else {
			val = this.series['y'].minValue + this.series['y'].scale * i / ny;
			val = (isimp ? (isWeight ? totSTONEtoKG(val) : CMfromIN(val)) : val);
			labs[i] = display_value(val, (isWeight ? 0 : 3), isimp, true, false);
		}
	}
	return labs;
}

Graph.prototype.createLabObject = function(arr, p1, clr, flag) {
	var isimp = this.series['y'].isimp;
	var isWeight = this.series['y'].isWeight;
	var obj = [];
	for (var i in arr) {
		pt = new Point(
			p1.x + (flag ? this.series['x'].intval * i - 5 : (isimp && isWeight ? -32 : -25)),
			p1.y + (flag ? 5 : -1 * (this.series['y'].scale * this.series['y'].intval * i + 10))
		);
		obj[i] = new Text(this.context, {'text' : arr[i], 'p1' : pt, 'size' : 14, 'bold' : false, 'colour' : clr, 'align' : "center" });
	}
	return obj;
}

Graph.prototype.createAxis = function(axis, title, clr, flag) {
	var isimp = this.series['y'].isimp;
	var isWeight = this.series['y'].isWeight;
	var idx = setIDX(flag);
	var p1 = this.PlotArea.getPoint(1);
	var p2 = this.PlotArea.getPoint((flag ? 3 : 0));
	axis[idx].Line = new Line(this.context, {'p1' : p1, 'p2' : [p2], 'stroke' : true});
	var pt = new Point((flag ? 0.5 : 0.035 - (isimp && isWeight ? 0.01 : 0)) * this.canvas.width, (flag ? 0.95 : 0.5) * this.canvas.height);
	axis[idx].title = new Text(this.context, {'text' : title, 'p1' : pt, 'size' : 18, 'colour' : clr, 'align' : "center", 'rot' : !flag});
	axis[idx].labels = this.createLabObject(this.createLabels(flag), p1, clr, flag);
}

Graph.prototype.createAxes = function(XTitle, YTitle, clr) {
	var axis = [];
	axis['x'] = [];
	axis['y'] = [];
	this.createAxis(axis, XTitle, clr, true);
	this.createAxis(axis, YTitle, clr, false);
	this.axis = axis;
}

Graph.prototype.setGL = function(gl, intval, nticks, clr, flag) {
	var idx = setIDX(flag);
	var k = 0;
	var end = (flag ? nticks.x : nticks.y);
	var inter = (flag ? intval.x : intval.y);
	for (var i = 0; i <= end; i++) {
		k = Math.round((flag ? this.PlotArea.p1.x : this.PlotArea.p1.y) + inter * i);
		p1 = new Point((flag ? k : Math.round(this.PlotArea.getPoint(0).x)), (flag ? Math.round(this.PlotArea.getPoint(0).y) : k));
		p2 = new Point((flag ? k : Math.round(this.PlotArea.getPoint(3).x)), (flag ? Math.round(this.PlotArea.getPoint(3).y) : k));
		gl[idx][i] = new Line(this.context, {'p1' : p1, 'p2' : [p2], 'colour' : clr, 'stroke' : true});
	}
}

Graph.prototype.createGrid = function(clr) {
	var intval = new Point(this.series['x'].intval, this.series['y'].intval);
	var nticks = new Point(this.series['x'].nticks, this.series['y'].nticks);
	var gl = [];
	gl['x'] = [];
	gl['y'] = [];
	this.setGL(gl, intval, nticks, clr, true);  // Create X gridlines;
	this.setGL(gl, intval, nticks, clr, false); // Create Y gridlines;
	this.grid = gl;
}

Graph.prototype.createChartTitle = function(txt, clr) {
	var grd = this.context.createLinearGradient(0, 0.045 * this.canvas.height, 0, 0.045 * this.canvas.height + 25);
	grd.addColorStop(0, 'red');
	grd.addColorStop(1, 'green');
	this.title = new Text(this.context, {'text' : txt, 'p1' : new Point(0.5 * this.canvas.width, 0.045 * this.canvas.height), 'size' : 25, 'bold' : true, 'colour' : clr,'align' : "center",'bcolor' : grd, 'shadow' : true});
};

Graph.prototype.drawAxes = function() {
	this.axis['x'].Line.draw();
	this.axis['x'].title.draw();
	for (var i in this.axis['x'].labels) {
		this.axis['x'].labels[i].setColour();
		this.axis['x'].labels[i].draw();
	}
	this.axis['y'].Line.draw();
	this.axis['y'].title.draw();
	for (var i in this.axis['y'].labels) {
		this.axis['y'].labels[i].setColour();
		this.axis['y'].labels[i].draw();
	}
}

Graph.prototype.drawGrid = function() {
	for(var i in this.grid['x']) {
		this.grid['x'][i].draw();
	}
	for(var i in this.grid['y']) {
		this.grid['y'][i].draw();
	}
}

Graph.prototype.drawSeries = function() {
	this.drawDataPoints();
	this.drawDataLine();
}

Graph.prototype.drawDataPoints = function() {
	for (var i in this.DataPoints) {
		this.DataPoints[i].setColour();
		this.DataPoints[i].draw()
	}
}

Graph.prototype.draw = function() {
	this.ChartArea.draw();
	this.title.draw();
	this.PlotArea.draw();
	this.drawDataPoints();
	this.dataLine.draw();
	this.drawGrid();
	this.drawAxes();
}

Graph.prototype.getImageData = function(flag) {
	var iType = 'image/' + (flag ? 'jpe' : 'pn') + 'g';
	var cvs = this.canvas;
	return cvs.toDataURL(iType);
}

Graph.prototype.save = function() {
	var dataURL = this.getImageData(true);
	var url = "/Health/includes/save_chart.php";
	var fName = "/Health/images/" + this.printID + ".jpg";
	$.ajax({
		type: "POST",
		url: url,
		dataType: 'text',
		data: {
			fname: fName,
			src: dataURL
		}
	});
}

function Point(x, y) {
	this.x = (x === undefined ? 0 : x);
	this.y = (y === undefined ? 0 : y);
}

function setShadow(ctx, flag) {
	ctx.shadowColor = (flag ? '#666' : "black");
	ctx.shadowBlur = (flag ? 5 : 0);
	ctx.shadowOffsetX = (flag ? 5 : 0);
	ctx.shadowOffsetY = (flag ? 5 : 0);
}

function Shape(ctx, options) {
	this.ctx = ctx;
	setShadow(this.ctx, options['shadow']);
	this.clr = ((options['colour'] === undefined) ? "black" : options['colour']);
	this.lw = (options['lwidth'] === undefined ? 1 : options['lwidth']);
	this.ctx.lineWidth = this.lw;
	this.p1 = options['p1'];
	this.stroke = (options['stroke'] === undefined ? false : options['stroke']);
	this.setColour();
}

Shape.prototype.setColour = function() {
	if(this.stroke) {
		this.ctx.strokeStyle = this.clr;
	} else {
		this.ctx.fillStyle = this.clr;
	}
}

function Line(ctx, options) {
	Line.baseConstructor.call(this, ctx, options);
	this.p2 = options['p2'];
}

extendClass.extend(Line, Shape);

Line.prototype.draw = function() {
	this.ctx.strokeStyle = this.clr;
	this.ctx.lineWidth = this.lw;
	this.ctx.beginPath();
	this.ctx.moveTo(this.p1.x, this.p1.y);
	for(i in this.p2) {
		this.ctx.lineTo(this.p2[i].x, this.p2[i].y);
	};
	this.ctx.stroke();
}

function Arc(ctx, options) {
	Arc.baseConstructor.call(this, ctx, options);
	this.radius = options['radius'];
	this.start = Math.radians(options['start']);
	this.end = Math.radians(options['end']);
	this.cwise = (options['cwise'] === undefined ? false : options['cwise']);
}

extendClass.extend(Arc, Shape);

Arc.prototype.draw = function() {
	this.ctx.beginPath();
	this.ctx.arc(this.p1.x, this.p1.y, this.radius, this.start, this.end, this.cwise);
	if(this.stroke) {
		this.ctx.stroke();
	} else {
		this.ctx.closePath();
		this.ctx.fill();
	}
}

function Circle(ctx, options) {
	Circle.baseConstructor.call(this, ctx, options);
	this.radius = options['radius'];
	this.start = 0;
	this.end = Math.radians(360);
	this.cwise = false;
}

extendClass.extend(Circle, Arc);

function Rect(ctx, options) {
	Rect.baseConstructor.call(this, ctx, options);
	if(options['p2'] === undefined) {
		this.width = options['width'];
		this.height = options['height'];
	} else {
		this.width = Math.abs(options['p2'].x - this.p1.x);
		this.height = Math.abs(options['p2'].y - this.p1.y);
		this.p1 = new Point(Math.min(this.p1.x, options['p2'].x), Math.min(this.p1.y, options['p2'].y));
	}
}

extendClass.extend(Rect, Shape);

Rect.prototype.getPoint = function(n) {
	var x, y;
	switch(n) {
		case 0:
			return this.p1;			// TL Corner
			break;
		case 1:
			x = this.p1.x;			// BL Corner
			y = this.p1.y + this.height;
			break;
		case 2:
			x = this.p1.x + this.width;	// TR Corner
			y = this.p1.y;
			break;
		default:
			x = this.p1.x + this.width;	// BR Corner
			y = this.p1.y + this.height;
			break;
	}
	return new Point(x, y);
}

Rect.prototype.draw = function() {
	this.setColour();
	if(this.stroke) {
		this.ctx.strokeRect(this.p1.x, this.p1.y, this.width, this.height);
	} else {
		this.ctx.fillRect(this.p1.x, this.p1.y, this.width, this.height);
	}
}

function Text(ctx, options) {
	Text.baseConstructor.call(this, ctx, options);
	default_args = {
		'text'		:	"",
		'font'		:	"Arial",
		'size'		:	10,
		'italics'	:	false,
		'bold'		:	true,
		'bcolor'	:	false,
		'align'		:	"left",
		'rot'		:	false,
		'shadow'	: 	false
	};
	for(var i in default_args) {
		if(typeof options[i] == "undefined") options[i] = default_args[i];
	};
	this.shadow = options['shadow'];
	this.txt = options['text'];
	this.font = options['font'];
	this.size = options['size'];
	this.isItalics = options['italics'];
	this.bcolor = options['bcolor'];
	this.align = options['align'];
	this.rot = options['rot'];
	this.isBold = options['bold'];
	this.ctx.textAlign = options['align'];
	this.setFont();
}

extendClass.extend(Text, Shape);

Text.prototype.setFont = function() {
	this.ctx.font = (this.isItalics ? 'italics ' : '') + (this.isBold ? 'bold ' : '') + this.size.toFixed() + "px " + this.font;
}

Text.prototype.draw = function() {
	this.setFont();
	var txtWidth = this.ctx.measureText(this.txt).width;
	var txtHeight = 1.25 * this.size;
	var pt = new Point(this.p1.x - txtWidth / 2 - 0.125 * this.size, this.p1.y);
	if (this.bcolor != false) {
		txtWidth += 0.375 * this.size;
		(new Rect(this.ctx, {'p1' : pt, 'width' : txtWidth, 'height': txtHeight, 'colour' : this.bcolor, 'shadow' : this.shadow})).draw();
	};
	this.ctx.fillStyle = this.clr;
	if(this.rot) {
		this.ctx.save();
		this.ctx.translate(this.p1.x, this.p1.y);
		this.ctx.rotate(-Math.radians(90));
	};
	this.ctx.fillText(this.txt,(this.rot ? 0 : this.p1.x),(this.rot ? 0 : this.p1.y + 0.75 * txtHeight));
	if(this.rot) this.ctx.restore();
	setShadow(this.ctx, false);
}

function dump(obj) {
	var out = '';
	for (var i in obj) {
		out += i + ": " + obj[i] + "\n";
	}

	var pre = document.createElement('pre');
	pre.innerHTML = out;
	document.body.appendChild(pre)
}

function zerofill(num, len) {

	var res = num.toString();
	var pad = len - res.length;
	
	while(pad > 0) {
		res = '0' + res;
		pad--;
	}
	
	return res;
}
