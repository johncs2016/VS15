<?php

require_once("OrbitalElements.php");

class OEJupiter extends OrbitalElements implements IOrbitalElements {

    public function __construct($year, $month, $day, $hour = 0, $minute = 0, $second = 0) { 
        parent::__construct($year, $month, $day, $hour, $minute, $second);
    }

    public function getN() { // longitude of the ascending node
        return $this->fix_range(100.4542 + 2.76854E-5 * $this->getDateObject()->getJD2000());
    }
    
    public function geti() { // inclination to the ecliptic (plane of the Earth's orbit)
        return 1.3030 - 1.557E-7 * $this->getDateObject()->getJD2000();
    }
    
    public function getw() {// argument of perihelion
        return $this->fix_range(273.8777 + 1.64505E-5 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    } 
    
    public function geta() { // semi-major axis, or mean distance from Sun
        return 5.20256; // (AU)
    }
    
    public function gete() { // eccentricity (0=circle, 0-1=ellipse, 1=parabola)
        return 0.048498 + 4.469E-9 * $this->getDateObject()->getJD2000();
    }
    
    public function getM() { // mean anomaly (0 at perihelion; increases uniformly with time)
        return $this->fix_range(19.8950 + 0.0830853001 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    }
}
