require("../scripts/conversions.js");

function display_value(value, v, isimp, flag, flag2) {
	value = Number(value);
	var factor = (flag2 ? 10 : 1);
	var nonimp_units = ['kg.', 'm.', ' km.', 'cm.'];
	var imp_units = [['st.', 'lb.'], ['ft.', 'in.'], ' miles.', 'in.'];
	var units = (isimp ? imp_units[v] : nonimp_units[v]);
	switch(v) {
		case 0:
			if(isimp) {
				if(flag) {
					var st = intSTONEfromKG(value);
					var lb = Math.round(factor * intPOUNDfromKG(value)) / factor;
				} else {
					var st = Math.floor(Math.round(14 * value) / 14);
					var lb = Math.round(factor * 14 * (value - st)) / factor;
				}
				if (lb == 14) {
					st++;
					lb = 0;
				}
				var res = st + units[0] + ' ' + lb.toFixed(flag2 ? 1 : 0) + units[1];
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
					var ins = Math.round(12 * (value - ft)) % 12;
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
	var zero = display_value(0, v, isimp, false, true);
	var res = display_value(Math.abs(diff), v, isimp, true, true);
	return (res == zero ? "No Change" : (lost ? "Lost" : "Gained") + ' ' + res);
}

