<?php

class coordinates2D {
    protected $_r, $_alpha;
    
    public function getR() {
        return $this->_r;
    }
    
    public function setR($r) {
        $this->_r = $r;
    }
    
    public function getAlpha() {
        return $this->_alpha;
    }
    
    public function setAlpha($alpha) {
        $this->_alpha = $alpha;
    }
    
    public function getX() {
        return $this->getR() * cos($this->getAlpha());
    }
    
    public function getY() {
        return $this->getR() * sin($this->getAlpha());
    }
    
    protected function __construct() {
        
    }
    
    public static function createFromPolar($r = 0, $alpha = 0) {
        
        $instance = new self();
        
        $instance->setR($r);
        $instance->setAlpha($alpha);
        
        return $instance;
    }
    
    public static function createFromCartesian($x = 0, $y = 0) {
        
        $instance = new self();
        
        $instance->setR(pow($x, 2) + pow($y, 2));
        $instance->setAlpha(atan2($y, $x));
        
        return $instance;
    }
}
