<?php
	class conversions {
		
		const MMtoINCHES = 0.03937;
		const MtoYARDS = 1.0936;
		const KMtoMILES = 0.6214;
		
		const INtoCM = 2.54;
		const FTtoM = 0.3048;
		const YARDStoM = 0.9144;
		const MILEStoKM = 1.6093;
		const NAUTtoKM = 1.853;
		
		const MGtoGRAIN = 0.0154;
		const GtoOUNCE = 0.0353;
		const KGtoPOUND = 2.2046;
		const TONNEtoTON = 0.9842;
		
		const OUNCEtoG = 28.35;
		const POUNDtoKG = 0.4536;
		const STONEtoKG = 6.3503;
		const CWTtoKG = 50.802;
		const TONtoTONNE = 1.016;
		
		private function __construct() {
			
		}
		
		private function __clone() {
			
		}
		
		public static function MtoFEET($x) {
			return 3 * $x * self::MtoYARDS;
		}
		
		public static function MtoIN($x) {
			return 12 * self::MtoFEET($x);
		}
		
		public static function intFTfromM($x) {
			return floor(self::MtoFEET($x));
		}
		
		public static function intINfromM($x) {
			return round(12 * (self::MtoFEET($x) - self::intFTfromM($x)));
		}
		
		public static function KMfromMILE($x) {
			return $x * self::KMtoMILES;
		}
		
		public static function MILEfromKM($x) {
			return $x * self::KMtoMILES;
		}
		
		public static function INfromCM($x) {
			return $x * (10 * self::MMtoINCHES);
		}
		
		public static function CMfromIN($x) {
			return $x * self::INtoCM;
		}
		
		public static function totPOUNDfromKG($x) {
			return $x * self::KGtoPOUND;
		}
		
		public static function STONEfromKG($x) {
			return self::totPOUNDfromKG($x) / 14;
		}
		
		public static function intPOUNDfromKG($x) {
			return 14 * (self::STONEfromKG($x) - self::intSTONEfromKG($x));
		}
		
		public static function intSTONEfromKG($x) {
			return floor(self::STONEfromKG($x));
		}
		
		public static function totPOUNDStoKG($x) {
			return $x * self::POUNDtoKG;
		}
	}
?>
