<?php

namespace App;

use Illuminate\Support\Arr;

class Student{
    private $name;
    private $presence;

    public function __construct(){
        $this->name = '';
        $this->presence = [];
    }

    public function getName(){
        return $this->name;
    }

    public function getPresence(){
        return $this->presence;
    }

    public function setName(String $name){
        $this->name = $name;
    }

    public function setPresence(Array $presence){
        $this->presence = $presence;
    }

    /**
     * Get minutes of presence from student object
     * 
     * @return int
     */
    public function minutesOfPresence(){
        $minutes = 0;

        foreach($this->presence as $presence){
            $hourIni = strtotime($presence["date"]." ".$presence["hourIni"]);
            $hourFin = strtotime($presence["date"]." ".$presence["hourFin"]);
            $diff = round(abs($hourIni - $hourFin) / 60);
            
            $minutes += $diff;
        }

        return $minutes;
    }

    /**
     * Get days of presence from student object
     * 
     * @return int
     */
    public function daysOfPresence(){
        $days = 0;
        $previousDay = null;
        
        $sortedPresence = array_values(Arr::sort($this->presence, function ($value) {
            return $value['date'];
        }));

        foreach($sortedPresence as $presence){
            if($presence["date"] != $previousDay){
                $days += 1;
            }
            
            $previousDay = $presence["date"];
        }

        return $days;
    }
}
