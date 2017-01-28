<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\Server;
use App\Models\ServerTask;
use App\Models\ServerUser;
use App\Models\User;
use Illuminate\Console\Command;

class Tools extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webdb:tools {cmd}';

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
        $command = $this->argument('cmd');

        switch($command)
        {
            case 'linux-names':
                $this->linux_names();
                break;
            case 'linux-users':
                $this->linux_users();
                break;
            case 'double-linux-names':
                $this->double_linux_names();
                break;
            case "cleanup":
                $this->cleanup();
                break;
            case "enable-ftp":
                $this->enable_ftp();
                break;
            default:
                $this->warn("Unknown command ".$command);
        }
    }

    function enable_ftp()
    {

        foreach(Server::where('state', 'running')->where('id',64)->get() as $server)
        {

            $ftp_configuration = [
                "# webdb changes",
                "write_enable=YES",
                "local_umask=002",
                "rsa_cert_file=/etc/letsencrypt/live/".$server->hostname."/fullchain.pem",
                "rsa_private_key_file=/etc/letsencrypt/live/".$server->hostname."/privkey.pem",
                "ssl_enable=YES",
                "allow_anon_ssl=NO",
                "force_local_data_ssl=YES",
                "force_local_logins_ssl=YES",
                "ssl_tlsv1=YES",
                "ssl_sslv2=NO",
                "ssl_sslv3=NO",
                "require_ssl_reuse=NO",
                "ssl_ciphers=HIGH"];
            $ftp_configuration = implode('\n', $ftp_configuration);


            $commands = [];
            $commands[] = "unset HISTFILE";
            $commands[] = "apt-get install -y vsftpd";
            $commands[] = 'echo -e "'.$ftp_configuration.'" >> /etc/vsftpd.conf';
            $commands[] = "systemctl restart vsftpd";


            \SSH::into($server->name)->run($commands);

        }
    }

    function linux_users()
    {
        /* For all groups.. */
        foreach(Group::get() as $group)
        {
            if(!$group->server->created)
                continue;

            /* Find the user of the assistant */
            if($group->assistant_id != null)
            {
                $su = ServerUser::where('user_id',$group->assistant_id)->where('server_id',$group->server->id)->first();

                /* Not created, create! */
                if(!$su && $group->assistant_id)
                {
                    $su = new ServerUser();
                    $su->user_id = $group->assistant_id;
                    $su->server_id = $group->server->id;
                    $su->created = 0;
                    $su->save();
                }
            }

            /* All students */
            foreach($group->students as $student)
            {
                /* Check if not already created */
                $su = ServerUser::where('user_id',$student->user_id)->where('server_id',$group->server->id)->first();

                /* Not created, create! */
                if(!$su)
                {
                    $su = new ServerUser();
                    $su->user_id = $student->user_id;
                    $su->server_id = $group->server->id;
                    $su->created = 0;
                    $su->save();
                }
            }
        }
    }


    function linux_names()
    {
        foreach(User::where('linux_name','')->get() as $user)
        {
            if(!$user->student)
                $user->linux_name = $user->uvanetid;
            else
            {
                $user->linux_name = str_replace(" ","",$user->firstname);
            }

            $user->linux_name = strtolower($user->linux_name);
            $user->linux_name = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($user->linux_name, ENT_QUOTES, 'UTF-8'));

            $user->save();
        }
    }

    function double_linux_names()
    {
        /* For all groups.. */
        foreach(Group::all() as $group)
        {
            $linux_names = [];
            foreach($group->students as $student)
            {
                if(!in_array($student->user->linux_name, $linux_names))
                    $linux_names[] = $student->user->linux_name;
                else
                    echo "Group ".$group->name. " has multiple users with name ". $student->user->linux_name.".\n";
            }
        }
    }

    private function cleanup()
    {

        $servers = Server::get();

        /** @var Server[] $servers */
        foreach($servers as $server)
        {
            if($server->group_id == null)
                continue;

            $susers = $server->users()->where('created',1)->pluck('user_id')->all();

            $ids = $server->group->students->pluck('user_id')->all();
            if($server->group->assistant_id != null)
                $ids[] = $server->group->assistant_id;


            foreach(array_diff($susers, $ids) as $uid)
            {
                $user = User::find($uid);;
                if($this->confirm("Do you want to remove ".$user->name." from ".$server->name."?"))
                {
                    $su = ServerUser::where('user_id',$uid)->where('server_id',$server->id)->first();
                    $su->created = 2;
                    $su->save();
                }
            }

        }
    }
}
