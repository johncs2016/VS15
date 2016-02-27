<?php

require_once("OrbitalElements.php");

class OESaturn extends OrbitalElements implements IOrbitalElements {

    public function __construct($year, $month, $day, $hour = 0, $minute = 0, $second = 0) { 
        parent::__construct($year, $month, $day, $hour, $minute, $second);
    }

    public function getN() { // longitude of the ascending node
        return $this->fix_range(113.6634 + 2.38980E-5 * $this->getDateObject()->getJD2000());
    }
    
    public function geti() { // inclination to the ecliptic (plane of the Earth's orbit)
        return 2.4886 - 1.081E-7 * $this->getDateObject()->getJD2000();
    }
    
    public function getw() {// argument of perihelion
        return $this->fix_range(339.3939 + 2.97661E-5 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    } 
    
    public function geta() { // semi-major axis, or mean distance from Sun
        return 9.55475; // (AU)
    }
    
    public function gete() { // eccentricity (0=circle, 0-1=ellipse, 1=parabola)
        return 0.055546 - 9.499E-9 * $this->getDateObject()->getJD2000();
    }
    
    public function getM() { // mean anomaly (0 at perihelion; increases uniformly with time)
        return $this->fix_range(316.9670 + 0.0334442282 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    }
}
