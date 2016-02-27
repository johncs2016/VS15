var MMtoINCHES = 0.03937;
var MtoYARDS = 1.0936;
var KMtoMILES = 0.6214;

var INtoCM = 2.54;
var FTtoM = 0.3048;
var YARDStoM = 0.9144;
var MILEStoKM = 1.6093;
var NAUTtoKM = 1.853;

var MGtoGRAIN = 0.0154;
var GtoOUNCE = 0.0353;
var KGtoPOUND = 2.2046;
var TONNEtoTON = 0.9842;

var OUNCEtoG = 28.35;
var POUNDtoKG = 0.4536;
var STONEtoKG = 6.3503;
var CWTtoKG = 50.802;
var TONtoTONNE = 1.016;

function MtoFEET(x) {
	return 3 * x * MtoYARDS;
}

function MtoIN(x) {
	return 12 * MtoFEET(x);
}

function intFTfromM(x) {
	return Math.floor(MtoFEET(x));
}

function intINfromM(x) {
	return Math.round(12 * (MtoFEET(x) - intFTfromM(x)));
}

function KMfromMILE(x) {
	return x * KMtoMILES;
}

function MILEfromKM(x) {
	return x * KMtoMILES;
}

function INfromCM(x) {
	return x * (10 * MMtoINCHES);
}

function CMfromIN(x) {
	return x * INtoCM;
}

function totPOUNDfromKG(x) {
	return x * KGtoPOUND;
}

function STONEfromKG(x) {
	return totPOUNDfromKG(x) / 14;
}

function intPOUNDfromKG(x) {
	return (Math.round(totPOUNDfromKG(x)) % 14);
}

function intSTONEfromKG(x) {
	return Math.floor(Math.round(totPOUNDfromKG(Math.abs(x))) / 14);
}

function totPOUNDStoKG(x) {
	return x * POUNDtoKG;
}

function totSTONEtoKG(x) {
	return x * STONEtoKG;
}
