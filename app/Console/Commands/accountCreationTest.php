<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DoAccountCreation;

use Queue;

class accountCreationTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:accountCreation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests creating an account under a random name.';

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
        $mon = $date["mon"];
        $mday = $date["mday"];
        $year = $date["year"];
        $sec = $date["seconds"];
        $min = $date["minutes"];
        $hr = $date["hours"];
        Queue::push(new DoAccountCreation("netID$mon$mday$year$hr$min$sec", "bob", "fake", "amg295@cornell.edu", env('CU_REALM')));
    }
}
