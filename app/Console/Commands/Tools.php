<?php

namespace App\Console\Commands;

use App\Models\Group;
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
            case 'create-tasks':
                $this->create_tasks();
                break;

        }
    }

    function linux_users()
    {
        /* For all groups.. */
        foreach(Group::all() as $group)
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

    function create_tasks()
    {
        for($i = 6; $i <= 49; $i++)
        {
            $t = new ServerTask();
            $t->server_id = $i;
            $t->action = "create";
            $t->save();

            $t = new ServerTask();
            $t->server_id = $i;
            $t->action = "start";
            $t->save();

            $t = new ServerTask();
            $t->server_id = $i;
            $t->action = "configure";
            $t->save();
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
}
