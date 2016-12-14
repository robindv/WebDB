<?php

namespace App\Console\Commands;

use App\Models\Server;
use Illuminate\Console\Command;

class ServerSSLCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webdb:ssl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update information about SSL certificates';

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
        Server::whereNotNull('cloudstack_id')->where('state','running')->get()->each( function($server) {
            $server->refresh_ssl_info();
        });

    }
}
