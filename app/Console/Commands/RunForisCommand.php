<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class RunForisCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foris:run { file }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command execute foris test';

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
        $file = fopen(storage_path('app/'.$this->arguments()["file"]), "r");

        while(!feof($file)) {
            $line = fgets($file);
            $command = explode(' ', $line);

            $this->executeCommand($command[0], $line);
        }

        fclose($file);

        $this->generateReport();

        Cache::flush();
    }

    /**
     * Execute foris command in file.
     *
     * @param String $commandType
     * @param String $command
     * @return void
     */
    protected function executeCommand(String $commandType, String $command){
        switch ($commandType) {
            case 'Student':
                Artisan::call('foris:'.$command);
                break;

            case 'Presence':
                Artisan::call('foris:'.$command);
                break;
        }
    }

    /**
     * Execute genetation of report from student presence.
     *
     * @return void
     */
    protected function generateReport(){
        $minutes = null;
        $days = null;
        $result = [];

        if(Cache::has('students')){
            $data = Cache::get('students');

            foreach($data as $student){
                $minutes = $student->minutesOfPresence();
                $days = $student->daysOfPresence();

                array_push($result, [
                    "message" => $student->getName().': '.$minutes.' minutes in '.$days.' days.',
                    "minutes" => $minutes,
                    "days"    => $days
                ]);
            }

            $sortedForMinutes = array_reverse(array_values(Arr::sort($result, function ($value) {
                return $value['minutes'];
            })));

            foreach ($sortedForMinutes as $students) {
                $this->info($students["message"]);
            }
        }
    }
}
