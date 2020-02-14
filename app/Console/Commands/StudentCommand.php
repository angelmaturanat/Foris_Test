<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

use \App\Student;
use stdClass;

class StudentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foris:Student { student }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store student information for future works.';

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
        //Creamos el objeto con el estudiante
        $studentArray = $this->createStudent();

        //Insertamos ese objeto estudiante en caché
        $this->putStudent($studentArray);
    }

    /**
    * Execute creation of student object
    * @return object
    */
    protected function createStudent(){
        $student = new Student();
        $student->setName($this->arguments()["student"]);

        return $student;
    }

    /**
    * Execute put student information in Cache
     *
     * @param \App\Student $student
     * @return void
     */
    protected function putStudent(Student $student){
        $data = Array();
        $studentName = $this->arguments()["student"];

        //We check if the student structure exists in the cache.
        if(Cache::has('students')){
            //If there is a structure, we store them in a variable.
            $data = Cache::get('students');

            //We check that the student does not exist in cache before adding it.
            if(!Arr::has($data, $studentName)){
                $data[$studentName] = $student;

                Cache::flush();
                Cache::put('students', $data);
            }
        }else{
            //If the cached structure does not exist, the first record is saved.
            $data[$studentName] = $student;
            Cache::put('students', $data);
        }

        $this->info('The student '.$studentName.' was added successfully.');
    }
}
