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
	document.getElementById("pounds").value = lb.toFixed(0);
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
	var lb = intPOUNDfromKG(kg);
	document.getElementById("stone").value = st;
	document.getElementById("pounds").value = lb;
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
	toggle_fields();
	$('input[type=date]').on('click', function(event) {
    event.preventDefault();
    });
	$('#odate').datepicker({ dateFormat: "dd/mm/yy", showButtonPanel: true }).val();
});

