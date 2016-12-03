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
        $task = ServerTask::where('status','0')->first();

        if($task)
        {
            $task->server->refresh();

            switch($task->action)
            {
                case "start":
                    $this->start($task);
                    break;
                case "create":
                    $this->create($task);
                    break;
                case "ssl":
                    $this->ssl($task);
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
        $task->status = 1;
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
        if($task->server->created)
        {
            $this->error($task->server->name.' already created.');
            return;
        }
        $this->info('Creating '.$task->server->name);

        $state = $task->server->deploy();
        if($state === true)
            $task->status = 1;
        else
        {
            $task->status = 2;
            $this->error($state);
        }
        $task->save();
    }


    /**
     * configure the server
     *
     * @var ServerTask
     */
    private function configure_server($task)
    {
        $server = $task->server;

        /* Check if the server has already been configured */
        if($server->configured == 1)
        {
            $this->error($server->name.' already configured.');
            return;
        }

        /* Make sure the server is up and running */
        if($server->state != 'Running')
        {
            $this->error($server->name.' is not running.');
            return;
        }

        /* Generate passwords for phpmyadmin and debian-sys-maint mysql-users */
        $mysql_debian_pass = $this->rand_string(14);

        /* Only use the prefix of the full hostname */
        $hostname = explode(".",$server->hostname)[0];
        $template_hostname = env('WEBDB_TEMPLATE_HOSTNAME');

        /* Connect to server */
        $commands = [];

        /* Hostname */
        $commands[] = "echo ".$hostname." > /etc/hostname";
        $commands[] = 'echo -e "127.0.0.1\tlocalhost\n'.$server->ip_address.'\t'.$hostname.' '.$server->hostname.'" > /etc/hosts';

        /* phpmyadmin */
        $commands[] = "sed -i 's/webdb-do-replace-00000000000000000000000000000000000/".$this->rand_string(32)."/g' /var/www/phpmyadmin/config.inc.php";

        /* Apache */
        $commands[] = "sed -i 's/webdb-2017-template/".$hostname."/g' /etc/apache2/sites-available/webdb.conf";

        /* MySQL */
        $commands[] = 'export mp=`cat /etc/mysql/debian.cnf | grep -m 1 \'password\' | awk -F\'= \' \'{print $2}\'`';
        $commands[] = 'mysql -u debian-sys-maint -p${mp}'." mysql -e \"SET PASSWORD FOR 'debian-sys-maint'@'localhost' = PASSWORD('".$mysql_debian_pass."');FLUSH PRIVILEGES\"";
        $commands[] = 'sed -i "s/${mp}/'.$mysql_debian_pass.'/g" /etc/mysql/debian.cnf';

        /* Security fixes */
        $commands[] = "passwd --lock root";
        $commands[] = "rm /etc/ssh/ssh_host_*";
        $commands[] = "/usr/sbin/dpkg-reconfigure openssh-server";

        /* Clear some log files */
        $commands[] = "cat /dev/null > /var/log/auth.log";
        $commands[] = "cat /dev/null > /var/log/syslog";
        $commands[] = "cat /dev/null > /var/log/apache2/access.log";
        $commands[] = "cat /dev/null > /var/log/apache2/error.log";

        $commands[] = "rm -f /root/.viminfo";
        $commands[] = "rm -f /root/.bash_history";
        $commands[] = "history -c && history -w && reboot";

        SSH::into($server->name)->run($commands);

        /* Store server as 'configured' */
        $task->server->configured = 1;
        $task->server->save();

        /* Set task as completed */
        $task->status = 1;
        $task->save();

    }

    private function ssl($task)
    {
        $commands[] = "certbot --apache --non-interactive --register-unsafely-without-email --agree-tos -d ".$task->server->hostname;
        SSH::into($task->server->name)->run($commands);

        /* Set task as completed */
        $task->status = 1;
        $task->save();
    }
}
