<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SSHServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if(! \Schema::hasTable("servers"))
            return;

        $connections = [];
        foreach(\App\Models\Server::get() as $server)
        {
            if(!$server->created)
                continue;

            $connections[$server->name] = ['host'      => $server->ip_address,
                                           'username'  => 'root',
                                           'key'       => env('SSH_KEYPATH'),
                                           'keyphrase' => env('SSH_PASSPHRASE'),
                                           'timeout'   => 10];
        }
        config(['remote.connections' => $connections]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
