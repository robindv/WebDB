<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SSH;

class ServerUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webdb:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create users on the servers.';

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
     * Create random password of given length.
     *
     * @return string
     */
    private function rand_string($length)
    {
        $chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789";
        return substr(str_shuffle($chars),0,$length);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Creating users..');

        $tasks = \App\Models\ServerUser::with('server','user')->where('created','=','0')->get();

        if(!count($tasks))
        {
            echo $this->info('All users are created.');
            return;
        }

        $i = 0;
        foreach($tasks as $task)
        {
            if($task->server->state != 'Running')
            {
                $this->error($task->server->name .' not running.');
                continue;
            }

            if($task->server->configured != '1')
            {
                $this->error($task->server->name .' not configured.');
                continue;
            }
            $i++;

            /* Generate a password */
            $task->password = $this->rand_string(8);
            $task->username = $task->user->linux_name;


            /* Connect to the server */
            $commands = [];
            $commands[] = "useradd -g users -G sudo -s /bin/bash -m -p`mkpasswd ".$task->password."` ".$task->username;
            $commands[] = "mysql -u debian-sys-maint -p".$task->server->mysql_debian_pass." mysql -e \"CREATE USER '".$task->username."'@'localhost' IDENTIFIED BY '".$task->password."'; GRANT ALL PRIVILEGES ON *.* TO '".$task->username."'@'localhost' WITH GRANT OPTION; FLUSH PRIVILEGES\"";
            $commands[] = "mkdir /home/".$task->username."/public_html";
            $commands[] = "chmod +x /home/".$task->username."/public_html";
            $commands[] = "echo 'Dit bestand staat in je <u>public_html</u> directory!' > /home/".$task->username."/public_html/test.html";
            $commands[] = "chown -R ".$task->username.":users /home/".$task->username."/public_html";
            $commands[] = "chmod g-w /home/".$task->username;
            $commands[] = "history -c && history -w";

            SSH::into($task->server->name)->run($commands);

            /* Done, set completed to 1 */
            $task->created = 1;
            $task->save();

            /* Do not create more then 10 users in one batch to avoid race conditions. */
            if($i == 10)
                break;
        }
    }
}
