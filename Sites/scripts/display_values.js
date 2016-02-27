require("../scripts/conversions.js");

function display_value(value, v, isimp, flag) {
	value = Number(value);
	var nonimp_units = ['kg.', 'm.', ' km.', 'cm.'];
	var imp_units = [['st.', 'lb.'], ['ft.', 'in.'], ' miles.', 'in.'];
	var units = (isimp ? imp_units[v] : nonimp_units[v]);
	switch(v) {
		case 0:
			if(isimp) {
				if(flag) {
					var st = intSTONEfromKG(value);
					var lb = intPOUNDfromKG(value);
				} else {
					var st = Math.floor(Math.round(14 * value) / 14);
					var lb = Math.round(14 * (value - st)) % 14;
				}
				var res = st + units[0] + ' ' + lb + units[1];
			} else {
				var res = value.toFixed(1) + units;
			}
			break;
		case 1:
			if(isimp) {
				if(flag) {
					var ft = intFTfromM(value);
					var ins = intINfromM(value);
				} else {
					var ft = Math.floor(Math.round(12 * value) / 12);
					var ins = Math.round(12 * (value - st)) % 12;
				}
				var res = ft + units[0] + ' ' + ins + units[1];
			} else {
				var res = value.toFixed(1) + units;
			}
			break;
		case 2:
			value = (isimp && flag ? MILEfromKM(value) : value);
			var res = value.toFixed(2) + units;
			break;
		default:
			value = (isimp && flag ? INfromCM(value) : value);
			var res = value.toFixed(1) + units;
			break;
	}
	return res;
}

function display_change(val1, val2, v, isimp) {
	var diff = Number(val2) - Number(val1);
	var lost = (diff < 0);
	var zero = display_value(0, v, isimp, false);
	var res = display_value(Math.abs(diff), v, isimp, true);
	return (res == zero ? "No Change" : (lost ? "Lost" : "Gained") + ' ' + res);
}

