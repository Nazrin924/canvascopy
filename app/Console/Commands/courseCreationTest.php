<?php

namespace App\Console\Commands;

use App\Jobs\DoCourseCreation;
use Illuminate\Console\Command;
use Queue;

class courseCreationTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:courseCreation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the creation of a Blackboard course using Adams NetID.';

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
        $date = getdate();
        $mon = $date['mon'];
        $mday = $date['mday'];
        $year = $date['year'];
        $sec = $date['seconds'];
        $min = $date['minutes'];
        $hr = $date['hours'];
        Queue::push(new DoCourseCreation("courseTest$mon$mday$year$hr$min$sec", "courseName$mon$mday$year$hr$min$sec", 'amg295', 'Test', env('CU_REALM')));
    }
}
