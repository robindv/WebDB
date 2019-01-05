<?php


namespace App\Console\Commands;


use App\Models\Group;
use App\Models\Server;
use Illuminate\Console\Command;

class LocalTask extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webdb:local';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Local command';

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
    }

}