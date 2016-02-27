<?php

require_once("coordinates2D.php");

class coordinates3D extends coordinates2D {
    protected $_theta;
    
    public function getTheta() {
        return $this->_theta;
    }
    
    public function setTheta($theta) {
        $this->_theta = $theta;
    }
    
    public function getX() {
        return $this->getR() * cos($this->getAlpha()) * cos($this->getTheta());
    }
    
    public function getY() {
        return $this->getR() * sin($this->getAlpha()) * cos($this->getTheta());
    }
    
    public function getZ() {
        return $this->getR() * sin($this->getTheta());
    }
    
    protected function __construct() {
        parent::__construct();
    }
    
    public static function createFromPolar($r = 0, $alpha = 0, $theta = 0) {
        
        $instance = new self();
        
        $instance->setR($r);
        $instance->setAlpha($alpha);
        $instance->setTheta($theta);
        
        return $instance;
    }
    
    public static function createFromCartesian($x = 0, $y = 0, $z = 0) {
        
        $instance = new self();
        
        $instance->setR(sqrt(pow($x, 2) + pow($y, 2) + pow($z, 2)));
        $instance->setAlpha(atan2($y, $x));
        $instance->setTheta(atan2($z, sqrt(pow($x, 2) + pow($y, 2))));
        
        return $instance;
    }
}
