<?php

namespace App\Console\Commands;

use App\Models\ServerTask;
use Illuminate\Console\Command;
use SSH;

class ServerTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webdb:tasks';

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
        /* Retrieve a task that is not completed yet */
        $task = ServerTask::where('completed','0')->first();

        if($task)
        {
            switch($task->action)
            {
                case "start":
                    $this->start($task);
                    break;
                case "create":
                    $this->create($task);
                    break;
                case "configure":
                    $this->configure_server($task);
                    break;
            }
        }
        else
        {
            $this->info('No tasks in queue.');
        }
    }

    /**
     * Start the server
     *
     * @var ServerTask
     */
    private function start($task)
    {

        /* Start the server */
        if($task->server->state == 'Running')
            $this->error($task->server->name.' already started.');
        else
        {
            /* Start the server */
            $task->server->start();
            $this->info($task->server->name .' started.');
        }
        $task->completed = 1;
        $task->save();
    }

    /**
     * create the server
     *
     * @var ServerTask
     */
    private function create($task)
    {
        /* Check if the has already server been created */
        if($task->server->created == 1)
        {
            $this->error($task->server->name.' already created.');
            return;
        }

        $this->info('Creating '.$task->server->name);

        /* Put parameters into a POST request */
        $fields['Name'] = $task->server->name;
        $fields['MacAddress'] = $task->server->mac_address;
        $fields['IPAddress'] = $task->server->ip_address;
        $fields['Memory'] = 2147483648;
        $fields['ImageName'] = env('WEBDB_IMAGE_NAME');

        $fields_string = "";
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        $fields_string = rtrim($fields_string, '&');

        /* Send request to the server */
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, "http://".env("WEBDB_API")."/vm");
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        //    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $result = curl_exec($ch);
        curl_close($ch);

        if($result == "true")
        {
            /* Done, store status in database */
            $task->server->created = 1;
            $task->server->save();

            $task->completed = 1;
            $task->save();
        }
    }


    /**
     * configure the server
     *
     * @var ServerTask
     */
    private function configure_server($task)
    {
        /* Check if the server has already been configured */
        if($task->server->configured == 1)
        {
            $this->error($task->server->name.' already configured.');
            return;
        }

        /* Make sure the server is up and running */
        if($task->server->state != 'Running')
        {
            $this->error($task->server->name.' is not running.');
            return;
        }

        /* Check that the server is assigned to a group */
        if(!$task->server->hostname)
        {
            $this->error($task->server->name. ' has not a hostname.');
            return;
        }

        /* Generate passwords for phpmyadmin and debian-sys-maint mysql-users */
        $task->server->mysql_debian_pass = $this->rand_string(14);

        /* Create the configuration file */
        $hostname = $task->server->hostname;

        /* Connect to server */
        $commands = [];
        $commands[] = "echo ".$hostname." > /etc/hostname";
        $commands[] = "sed -i 's/webdb0/".$hostname."/g' /etc/hosts";
        $commands[] = "sed -i 's/webdb0/".$hostname."/g' /etc/php/7.0/apache2/conf.d/mailcatcher.ini";
        $commands[] = "sed -i 's/webdb0/".$hostname."/g' /var/www/phpmyadmin/config.inc.php";
        $commands[] = "sed -i 's/127.0.1.1/".$task->server->ip_address."/g' /etc/hosts";
        $commands[] = "mysql -u debian-sys-maint -p".env('WEBDB_IMAGE_MYSQL_PASS')." mysql -e \"SET PASSWORD FOR 'debian-sys-maint'@'localhost' = PASSWORD('".$task->server->mysql_debian_pass."');FLUSH PRIVILEGES\"";
        $commands[] = "sed -i 's/".env('WEBDB_IMAGE_MYSQL_PASS')."/".$task->server->mysql_debian_pass."/g' /etc/mysql/debian.cnf";
        $commands[] = "passwd --lock root";
        $commands[] = "rm /root/.viminfo";
        $commands[] = "rm /root/.bash_history";
        $commands[] = "rm /etc/ssh/ssh_host_*";
        $commands[] = "/usr/sbin/dpkg-reconfigure openssh-server";
        $commands[] = "history -c && history -w && reboot";

        SSH::into($task->server->name)->run($commands);


        /* Store server as 'configured' */
        $task->server->configured = 1;
        $task->server->save();

        /* Set task as completed */
        $task->completed = 1;
        $task->save();

    }
}
