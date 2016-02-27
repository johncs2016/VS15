<?php

require_once("OrbitalElements.php");

class OEUranus extends OrbitalElements implements IOrbitalElements {

    public function __construct($year, $month, $day, $hour = 0, $minute = 0, $second = 0) { 
        parent::__construct($year, $month, $day, $hour, $minute, $second);
    }

    public function getN() { // longitude of the ascending node
        return $this->fix_range(74.0005 + 1.3978E-5 * $this->getDateObject()->getJD2000());
    }
    
    public function geti() { // inclination to the ecliptic (plane of the Earth's orbit)
        return 0.7733 + 1.9E-8 * $this->getDateObject()->getJD2000();
    }
    
    public function getw() {// argument of perihelion
        return $this->fix_range(96.6612 + 3.0565E-5 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    } 
    
    public function geta() { // semi-major axis, or mean distance from Sun
        return 19.18171 - 1.55E-8 * $this->getDateObject()->getJD2000(); // (AU)
    }
    
    public function gete() { // eccentricity (0=circle, 0-1=ellipse, 1=parabola)
        return 0.047318 + 7.45E-9 * $this->getDateObject()->getJD2000();
    }
    
    public function getM() { // mean anomaly (0 at perihelion; increases uniformly with time)
        return $this->fix_range(142.5905 + 0.011725806 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    }
}
