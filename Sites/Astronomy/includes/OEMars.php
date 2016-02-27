<?php

require_once("OrbitalElements.php");

class OEMars extends OrbitalElements implements IOrbitalElements {

    public function __construct($year, $month, $day, $hour = 0, $minute = 0, $second = 0) { 
        parent::__construct($year, $month, $day, $hour, $minute, $second);
    }

    public function getN() { // longitude of the ascending node
        return $this->fix_range(49.5574 + 2.11081E-5 * $this->getDateObject()->getJD2000());
    }
    
    public function geti() { // inclination to the ecliptic (plane of the Earth's orbit)
        return 1.8497 - 1.78E-8 * $this->getDateObject()->getJD2000();
    }
    
    public function getw() {// argument of perihelion
        return $this->fix_range(286.5016 + 2.92961E-5 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    } 
    
    public function geta() { // semi-major axis, or mean distance from Sun
        return 1.523688; // (AU)
    }
    
    public function gete() { // eccentricity (0=circle, 0-1=ellipse, 1=parabola)
        return 0.093405 + 2.516E-9 * $this->getDateObject()->getJD2000();
    }
    
    public function getM() { // mean anomaly (0 at perihelion; increases uniformly with time)
        return $this->fix_range(18.6021 + 0.5240207766 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    }
}
