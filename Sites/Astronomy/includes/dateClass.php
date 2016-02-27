<?php


class dateClass extends DateTime {
    
    protected $_show_Seconds = false;
    
    public function getShowSeconds() {
        return $this->_show_Seconds;
    }
    
    public function setShowSeconds($flag = false) {
        $this->_show_Seconds = $flag;
    }
    
    public function __construct($time = "now", $timezone = NULL) {
        parent::__construct($time, new DateTimeZone(is_null($timezone) ? ini_get("date.timezone") : $timezone));
    }
    
    public static function createFromJD($jd, $timezone = NULL) {

        $instance = new static();
        $instance->setTimestamp(86400 * ($jd - 2440587.5));
        $instance->setTimezone(new DateTimeZone(is_null($timezone) ? ini_get("date.timezone") : $timezone));

        return $instance;
    }
    
    public static function createFromJD2000($jd, $timezone = NULL) {
        return static::createFromJD($jd + 2451543.5, (is_null($timezone) ? ini_get("date.timezone") : $timezone));
    }
    
    public static function createFromYMD($year, $month, $day, $hour = 0, $minute = 0, $second = 0, $timezone = NULL) {

        $instance = new static();
        $instance->setTimezone(new DateTimeZone(is_null($timezone) ? ini_get("date.timezone") : $timezone));
        $instance->setDate($year, $month, $day);
        $instance->setTime($hour, $minute, $second);

        return $instance;
    }
    
    public function getDayNumber() {

        $N1 = floor(275 * $this->getMonth() / 9);
	$N2 = floor(($this->getMonth() + 9) / 12);
	$N3 = (1 + floor(($this->getYear() - 4 * floor($this->getYear() / 4) + 2) / 3));

        return ($N1 - ($N2 * $N3) + $this->getDay() - 30);

    }
    
    public function setToUTC() {
        $this->setTimezone(new DateTimeZone("UTC"));
    }
    
    public function setToLocalTime() {
        $this->setTimezone(new DateTimeZone(ini_get("date.timezone")));
    }
    
    public function dateFormat() {
        return 'j F Y';
    }
    
    public function timeFormat() {
        return ('g:i'. ($this->getShowSeconds() == true ? ':s' : '') . ' a');
    }
    
    public function dateTimeFormat() {
        return $this->dateFormat() . ' ' . $this->timeFormat();
    }
    
    public function __toString() {
        return $this->format($this->dateTimeFormat());
    }
    
    public function getDate() {
        return $this->format($this->dateFormat());
    }
    
    public function getTime() {
        return $this->format($this->timeFormat());
    }
    
    public function getYear() {
        return $this->format('Y');
    }
    
    public function getMonth() {
        return $this->format('n');
    }
    
    public function getDay() {
        return $this->format('j');
    }
    
    public function getHours() {
        return (int)$this->format('G');
    }
    
    public function getMinutes() {
        return (int)$this->format('i');
    }
    
    public function getSeconds() {
        return (int)$this->format('s');
    }
    
    public function getJD() {
        return (2440587.5 + ($this->getTimestamp() / 86400));
    }
    
    public function getJD2000() {
        return ($this->getJD() - 2451543.5);
    }
}
