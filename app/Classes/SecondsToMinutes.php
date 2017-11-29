<?php
/**
 * Created by PhpStorm.
 * User: olgakolos
 * Date: 29.11.17
 * Time: 16:19
 */
namespace App\Classes;
class SecondsToMinutes
{
    private $duration;
    private $minutes;
    private $seconds;

    public function __construct($duration)
    {
        $this->duration = $duration;
        $this->convert();
    }

    private function convert() {
        $this->minutes = floor($this->duration / 60);
        if($this->minutes < 10) {
            $this->minutes = "0" . $this->minutes;
        }
        $this->seconds = $this->duration % 60;
        if($this->seconds < 10) {
            $this->seconds = '0' . $this->seconds;
        }
    }

    public function getConversion() {
        return $this->minutes . ":" . $this->seconds;
    }
}