<?php

namespace App\Console\Commands;

use App\Models\ServerUser;
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
        ServerUser::with('server','user')->where('created','=','0')->get()->each( [$this, "create_user"]);
        ServerUser::with('server','user')->where('created','=','2')->get()->each( [$this, "delete_user"]);

    }

    function create_user(ServerUser $su)
    {
        if($su->server->state != 'Running' || ! $su->server->configured)
        {
            $this->error($su->server->name .' not running/configured.');
            return;
        }

        $su->password = $this->rand_string(14);
        $su->username = $su->user->linux_name;

        $commands = [];
        $commands[] = "unset HISTFILE";
        $commands[] = "useradd -g users -G sudo -s /bin/bash -m -p`mkpasswd ".$su->password."` ".$su->username;
        $commands[] = 'export mp=`cat /etc/mysql/debian.cnf | grep -m 1 \'password\' | awk -F\'= \' \'{print $2}\'`';
        $commands[] = 'mysql -u debian-sys-maint -p${mp}'." mysql -e \"CREATE USER '".$su->username."'@'localhost' IDENTIFIED BY '".$su->password."'; GRANT ALL PRIVILEGES ON *.* TO '".$su->username."'@'localhost' WITH GRANT OPTION; FLUSH PRIVILEGES\"";
        $commands[] = "mkdir /home/".$su->username."/public_html";
        $commands[] = "chmod +x /home/".$su->username."/public_html";
        $commands[] = "echo 'Dit bestand staat in je <u>public_html</u> directory!' > /home/".$su->username."/public_html/test.html";
        $commands[] = "chown -R ".$su->username.":users /home/".$su->username."/public_html";
        $commands[] = "chmod g-w /home/".$su->username;

        SSH::into($su->server->name)->run($commands);

        $su->created = 1;
        $su->save();

        $this->info("Created a user for ".$su->user->name ." on ".$su->server->hostname);

    }

    function delete_user(ServerUser $su)
    {
        if($su->server->state != 'Running' || ! $su->server->configured)
        {
            $this->error($su->server->name .' not running/configured.');
            return;
        }

        $commands = [];
        $commands[] = "unset HISTFILE";
        $commands[] = 'export mp=`cat /etc/mysql/debian.cnf | grep -m 1 \'password\' | awk -F\'= \' \'{print $2}\'`';
        $commands[] = 'mysql -u debian-sys-maint -p${mp}'." mysql -e \"DROP USER '".$su->username."'@'localhost'; FLUSH PRIVILEGES\"";
        $commands[] = "deluser --remove-home ".$su->username;

        SSH::into($su->server->name)->run($commands);

        $su->created = 3;
        $su->save();

        $this->info("Deleted ".$su->user->name ." from ".$su->server->hostname);
    }

}
