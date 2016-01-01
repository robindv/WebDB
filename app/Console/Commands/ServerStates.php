<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ServerStates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webdb:states';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $this->info('Retrieving server states from API.');

        $states = json_decode(file_get_contents('http://'.env("WEBDB_API").'/vm'));

        $this->info('Storing states into database...');

        foreach($states as $server)
        {
            $state = ['state'=>$server->State, 'uptime'=>$server->UpTime, 'memory'=>$server->MemoryAssigned];
            DB::table('servers')->where('mac_address',$server->MacAddress)->update($state);
        }

        $this->info('Success');
    }
}
