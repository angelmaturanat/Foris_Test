<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class PresenceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foris:Presence { student } { dayOfWeek } { date } { hourIni } { hourFin } { classroomNum }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for register student attendance.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $presence = $this->createPresence(
            $this->arguments()["dayOfWeek"],
            $this->arguments()["date"],
            $this->arguments()["hourIni"],
            $this->arguments()["hourFin"],
            $this->arguments()["classroomNum"]
        );

        $this->insertPresenceStudent($presence);
    }

    /**
     * Execute creation of presence for student object.
     *
     * @param Int $dayOfWeek
     * @param String $date,
     * @param String $hourIni,
     * @param String $hourFin,
     * @param String $classroomNum
     * @return array
     */
    protected function createPresence(Int $dayOfWeek, String $date, String $hourIni, String $hourFin, String $classroomNum){
        if($this->validatePresenceInfo($dayOfWeek, $date, $hourIni, $hourFin)){
            return [
                "dayOfWeek" => $dayOfWeek,
                "date" => $date,
                "hourIni" => $hourIni,
                "hourFin" => $hourFin,
                "classroomNum" => $classroomNum
            ];
        }
    }

    /**
     * Execute validation to presence data.
     *
     * @param Int $dayOfWeek
     * @param String $date,
     * @param String $hourIni,
     * @param String $hourFin,
     * @return boolean
     */
    protected function validatePresenceInfo(Int $dayOfWeek, String $date, String $hourIni, String $hourFin){
        //Validate that the indicated day of the week is correct.
        if($dayOfWeek < 1 || $dayOfWeek > 7){
            $this->error('Day of week not valid, must be between 1 and 7.');
            return false;
        }

        //Validate that the entry time is less than the exit time
        if(strtotime($hourIni) > strtotime($hourFin)){
            $this->error('The start time must be less that the end time.');
            return false;
        }

        //Validate that the time of entry and exit has a difference of minutes greater than 5
        $hourIni = strtotime($date." ".$hourIni);
        $hourFin = strtotime($date." ".$hourFin);
        $diff = round(abs($hourIni - $hourFin) / 60);

        if($diff <= 5){
            $this->error('The time margin between the start and end time must be more than 5 minutes.');
            return false;
        }

        return true;
    }

    /**
    * Execute insert presence in student data storaged in caché
     *
     * @param \App\Student $student
     * @return void
     */
    protected function insertPresenceStudent($presence){
        $data = Array();
        $studentName = $this->arguments()["student"];

        //We check if the student structure exists in the cache.
        if(Cache::has('students')){
            //If there is a structure, we store them in a variable.
            $data = Cache::get('students');

            //We check that the student exists in cache.
            if(Arr::has($data, $studentName)){
                $studentPresence = $data[$studentName]->getPresence();
                array_push($studentPresence, $presence);

                $data[$studentName]->setPresence($studentPresence);

                Cache::flush();
                Cache::put('students', $data);
                
                $this->info('The student presence was added successfully.');
            }else{
                //If there is no student we notify this.

                $this->error('The student '.$studentName.' not exists.');
            }
        }else{
            //If the cached structure does not exist it is notified that there are no students.
            $this->error('Not exists information of students.');
        }
    }
}
