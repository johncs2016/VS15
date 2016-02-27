<?php

require_once("OrbitalElements.php");

class OEVenus extends OrbitalElements implements IOrbitalElements {

    public function __construct($year, $month, $day, $hour = 0, $minute = 0, $second = 0) { 
        parent::__construct($year, $month, $day, $hour, $minute, $second);
    }

    public function getN() { // longitude of the ascending node
        return $this->fix_range(76.6799 + 2.46590E-5 * $this->getDateObject()->getJD2000());
    }
    
    public function geti() { // inclination to the ecliptic (plane of the Earth's orbit)
        return 3.3946 + 2.75E-8 * $this->getDateObject()->getJD2000();
    }
    
    public function getw() {// argument of perihelion
        return $this->fix_range(54.8910 + 1.38374E-5 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    } 
    
    public function geta() { // semi-major axis, or mean distance from Sun
        return 0.723330; // (AU)
    }
    
    public function gete() { // eccentricity (0=circle, 0-1=ellipse, 1=parabola)
        return 0.006773 - 1.302E-9 * $this->getDateObject()->getJD2000();
    }
    
    public function getM() { // mean anomaly (0 at perihelion; increases uniformly with time)
        return $this->fix_range(48.0052 + 1.6021302244 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    }
}
