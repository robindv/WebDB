<?php

namespace App\Console\Commands;

use App\Connectors\GitLabConnector;
use App\Connectors\GitLabUser;
use App\Models\Group;
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
            case "find_accounts":
                $this->find_accounts();
                break;
            case "create_accounts":
                $this->create_accounts();
                break;
            case "find_create_groups":
                $this->find_create_groups();
                break;
        }
    }

    private function find_accounts()
    {
        $connector = new GitLabConnector();

        $users = User::whereNull('gitlab_user_id')->where('role','&', User::student_role)->get();

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
        $connector = new GitLabConnector();

        $users = User::whereNull('gitlab_user_id')->where('role','&', User::student_role)->get();
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

    private function find_create_groups()
    {
        $connector = new GitLabConnector();

        $groups = Group::whereNull('gitlab_group_id')->get();

        foreach($groups as $group)
        {
            $gg = $connector->find_group_by_string($group->fullname);

            if($gg == null)
            {
                $this->info("No match for ". $group->fullname);

                $gg = $group->create_gitlab_group($connector);
                $this->info("Created a new group with id ".$gg->id);
                continue;
            }

            $this->info("Found ".$gg->name);
            $group->gitlab_group_id = $gg->id;
            $group->save();
        }
    }
}
