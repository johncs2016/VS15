<?php

require_once("OrbitalElements.php");

class OEMercury extends OrbitalElements implements IOrbitalElements {

    public function __construct($year, $month, $day, $hour = 0, $minute = 0, $second = 0) { 
        parent::__construct($year, $month, $day, $hour, $minute, $second);
    }

    public function getN() { // longitude of the ascending node
        return $this->fix_range(48.3313 + 3.24587E-5 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    }
    
    public function geti() { // inclination to the ecliptic (plane of the Earth's orbit)
        return 7.0047 + 5.00E-8 * $this->getDateObject()->getJD2000();
    }
    
    public function getw() {// argument of perihelion
        return $this->fix_range(29.1241 + 1.01444E-5 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    } 
    
    public function geta() { // semi-major axis, or mean distance from Sun
        return 0.387098; // (AU)
    }
    
    public function gete() { // eccentricity (0=circle, 0-1=ellipse, 1=parabola)
        return 0.205635 + 5.59E-10 * $this->getDateObject()->getJD2000();
    }
    
    public function getM() { // mean anomaly (0 at perihelion; increases uniformly with time)
        return $this->fix_range(168.6562 + 4.0923344368 * $this->getDateObject()->getJD2000(), 0, 360, 360);
    }
}
