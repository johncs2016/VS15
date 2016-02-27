<?php

interface IOrbitalElements {

    public function getN(); // longitude of the ascending node
    public function geti(); // inclination to the ecliptic (plane of the Earth's orbit)
    public function getw(); // argument of perihelion
    public function geta(); // semi-major axis, or mean distance from Sun
    public function gete(); // eccentricity (0=circle, 0-1=ellipse, 1=parabola)
    public function getM(); // mean anomaly (0 at perihelion; increases uniformly with time)

}
