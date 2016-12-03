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

        $connector = new \App\Connectors\CloudStackConnector();

        foreach($connector->list_virtual_machines()->listvirtualmachinesresponse->virtualmachine as $vm)
        {
            $state = ['name' => $vm->name, 'state'=>$vm->state, 'memory'=>$vm->memory, 'ip_address' => $vm->nic[0]->ipaddress];
            DB::table('servers')->where('cloudstack_id',$vm->id)->update($state);
        }

        $this->info('Success');
    }
}
