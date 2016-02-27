<?php

require_once('CRUD.php');

class AllWeeklyStepsCounts extends pdoClass {

    protected $_RowNumber;
    protected $_WeekCommencing;
    protected $_WeekEnding;
    protected $_TotalSteps;
    protected $_AverageSteps;

    public function __construct(array $properties) {
        parent::__construct($properties);
    }

    public function getRowNumber() {
        return $this->_RowNumber;
    }

    public function setRowNumber($rn) {
        $this->_RowNumber = $rn;
    }

    public function getWeekCommencing() {
        return $this->_WeekCommencing;
    }

    public function setWeekCommencing($wc) {
        $this->_WeekCommencing = $wc;
    }

    public function getWeekEnding() {
        return $this->_Weekending;
    }

    public function setWeekEnding($we) {
        $this->_WeekEnding = $we;
    }

    public function getTotalSteps() {
        return $this->_TotalSteps;
    }

    public function setTotalSteps($ts) {
        $this->_TotalSteps = $ts;
    }

    public function getAverageSteps() {
        return $this->_AverageSteps;
    }

    public function setAverageSteps($as) {
        $this->_AverageSteps = $as;
    }

}