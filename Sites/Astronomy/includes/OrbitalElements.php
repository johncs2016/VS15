<?php

require_once("dateClass.php");
require_once("IOrbitalElements.php");

abstract class OrbitalElements {
    
    protected $_dateObject;
    
    public function getDateObject() {
        return $this->_dateObject;
    }
    
    public function setDateObject($obj) {
        $this->_dateObject = $obj;
    }
    
    public function getECL() {
        return 23.4393 - 3.563E-7 * $this->getDateObject()->getJD2000();
    }
    
    public function __construct($year, $month, $day, $hour = 0, $minute = 0, $second = 0) {
        $this->setDateObject(dateClass::createFromYMD($year, $month, $day, $hour, $minute, $second));
    }
    
    protected function fix_range($val, $min, $max, $step) {
        while (($val < $min) || ($val > $max)) {
            if ($val < $min) {
                $val += abs($step);
            } elseif ($val > $max) {
                $val -= abs($step);
            }
        }
        return $val;
    }
}
