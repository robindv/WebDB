<?php

namespace App\Console\Commands;

use App\Connectors\GitLabUser;
use App\Models\User;
use Illuminate\Console\Command;

class GitLabTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webdb:gitlab {cmd}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GitLab tasks';

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
            case "find":
                $this->find_accounts();
                break;
            case "create":
                $this->create_accounts();
                break;
        }
    }

    private function find_accounts()
    {
        $connector = new \App\Connectors\GitLabConnector();

        $users = \App\Models\User::whereNull('gitlab_user_id')->where('role','&', User::student_role)->get();

        foreach($users as $user)
        {
            $gu = $connector->find_user_by_username($user->uvanetid);
//            $gu = $connector->find_user_by_string($user->name);

            if($gu == null) {
                $this->info("No match for ". $user->name ." (".$user->uvanetid.")");
                continue;
            }

            $this->info("Matched ".$user->name." with GitLab user ".$gu->id);
            $user->gitlab_user_id = $gu->id;
            $user->save();
        }
    }

    private function create_accounts()
    {
        $connector = new \App\Connectors\GitLabConnector();

        $users = \App\Models\User::whereNull('gitlab_user_id')->where('role','&', User::student_role)->get();
        foreach($users as $user)
        {
            $gu = new GitLabUser($connector);
            $gu->username = $user->uvanetid;
            $gu->email    = $user->email;
            $gu->name     = $user->name;
            $gu->save();

            $user->gitlab_user_id = $gu->id;
            $user->save();

            $this->info("Created ". $user->name ." (".$user->uvanetid.")");
        }



    }
}
