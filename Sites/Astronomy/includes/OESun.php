<?php

require_once("OrbitalElements.php");

class OESun extends OrbitalElements implements IOrbitalElements {

    public function __construct($year, $month, $day, $hour = 0, $minute = 0, $second = 0) { 
        parent::__construct($year, $month, $day, $hour, $minute, $second);
    }

    public function getN() { // longitude of the ascending node
        return 0;
    }
    
    public function geti() { // inclination to the ecliptic (plane of the Earth's orbit)
        return 0;
    }
    
    public function getw() {// argument of perihelion
        return $this->fix_range(282.9404 + 4.70935E-5 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    } 
    
    public function geta() { // semi-major axis, or mean distance from Sun
        return 1; // (AU)
    }
    
    public function gete() { // eccentricity (0=circle, 0-1=ellipse, 1=parabola)
        return 0.016709 - 1.151E-9 * $this->getDateObject()->getJD2000();
    }
    
    public function getM() { // mean anomaly (0 at perihelion; increases uniformly with time)
        return $this->fix_range(356.0470 + 0.9856002585 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    }
}
