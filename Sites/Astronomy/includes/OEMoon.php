<<?php

require_once("OrbitalElements.php");

class OEMoon extends OrbitalElements implements IOrbitalElements {

    public function __construct($year, $month, $day, $hour = 0, $minute = 0, $second = 0) { 
        parent::__construct($year, $month, $day, $hour, $minute, $second);
    }

    public function getN() { // longitude of the ascending node
        return $this->fix_range(125.1228 - 0.0529538083 * $this->getDateObject()->getJD2000());
    }
    
    public function geti() { // inclination to the ecliptic (plane of the Earth's orbit)
        return 5.1454;
    }
    
    public function getw() {// argument of perihelion
        return $this->fix_range(318.0634 + 0.1643573223 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    } 
    
    public function geta() { // semi-major axis, or mean distance from Sun
        return 60.2666; // (Earth radii)
    }
    
    public function gete() { // eccentricity (0=circle, 0-1=ellipse, 1=parabola)
        return 0.054900;
    }
    
    public function getM() { // mean anomaly (0 at perihelion; increases uniformly with time)
        return $this->fix_range(115.3654 + 13.0649929509 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    }
}
