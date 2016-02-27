require("../scripts/display_values.js");

function toggle_fields() {
	var cssprop = "display";
	var chkbox = document.getElementById("isimp");
	var isimp = chkbox.checked;
	var impprop = (isimp ? "inline" : "none");
	var props = (isimp ? "none" : "inline");
	$('.isimp').css(cssprop, impprop);
	$('.noimp').css(cssprop, props);
	if(isimp) {
		update_stlb();
		update_inches();
	} else {
		update_weight();
		update_waist();
	};
	var id = "#" + (isimp ? "stone" : "weight");
	$(id).focus();
}

function update_weight() {
	var st = document.getElementById("stone").value;
	var lb = document.getElementById("pounds").value;
	st = (st == "" ? 0 : Number(st));
	lb = (lb == "" ? 0 : Number(lb));
	var lbdec = 14 * st + lb;
	var kg = totPOUNDStoKG(lbdec);
	document.getElementById("weight").value = kg.toFixed(1);
	dispChange(true);
	document.getElementById("stone").value = st.toFixed(0);
	document.getElementById("pounds").value = lb.toFixed(1);
}

function update_waist() {
	var ins = document.getElementById("inches").value;
	ins = (ins == "" ? 0 : Number(ins));
	var cm = CMfromIN(ins);
	document.getElementById("waist").value = cm.toFixed(1);
	dispChange(false);
	document.getElementById("inches").value = ins.toFixed(1);
}

function update_stlb() {
	var kg = document.getElementById("weight").value;
	kg = (kg == "" ? 0 : Number(kg));
	var st = intSTONEfromKG(kg);
	var lb = Math.round(10 * intPOUNDfromKG(kg)) / 10;
	if (lb == 14) {
		st++;
		lb = 0;
	}
	document.getElementById("stone").value = st.toFixed(0);
	document.getElementById("pounds").value = lb.toFixed(1);
	dispChange(true);
	document.getElementById("weight").value = kg.toFixed(1);
}

function update_inches() {
	var cm = document.getElementById("waist").value;
	cm = (cm == "" ? 0 : Number(cm));
	var ins = INfromCM(cm);
	document.getElementById("inches").value = ins.toFixed(1);
	dispChange(false);
	document.getElementById("waist").value = cm.toFixed(1);
}

jQuery(function($){ //on document.ready
	$('#data_form').validate({
		rules: {
			odate: {
				required: true,
				dateITA: true
			},
			weight: {
				min: 0,
				number: true
			},
			stone: {
				min: 0,
				digits: true
			},
			pounds: {
				number: true,
				range: [0, 13.99]
			},
			waist: {
				min: 0,
				number: true
			},
			inches: {
				min: 0,
				number: true
			}
		},
		messages: {
			odate: {
				required: "Observation date must be entered",
				dateITA: "Please enter a valid date"
			},
			weight: {
				min: "Number of kilos must be a positive number",
				number:	"Weight must be a numeric value"
			},
			stone: {
				min: "Number of stones must be a positive number",
				digits:	"Number of stones must be an integer."
			},
			pounds: {
				number: "Number of pounds must be a numeric value",
				range: "Number of pounds must be between 0 and 13.99"
			},
			waist: {
				min: "Number of centimeters must be a positive number",
				number:	"Waist size must be a numeric value"
			},
			inches: {
				min: "Number of inches must be a positive number",
				number:	"Waist size must be a numeric value"
			}
		},
		errorContainer: $('#error_container'),
		errorLabelContainer: $('#error_container ul'),
		wrapper: 'li'
	});
	toggle_fields();
	$('input[type=date]').on('click', function(event) {
    event.preventDefault();
    });
	$('#odate').datepicker({ dateFormat: "dd/mm/yy", showButtonPanel: true }).val();
});

