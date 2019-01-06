<?php

namespace App\Console\Commands;

use App\Connectors\GitLabConnector;
use App\Connectors\GitLabGroup;
use App\Connectors\GitLabUser;
use App\Models\Group;
use App\Models\Student;
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
            case "add_assistants_to_groups":
                $this->add_assistants_to_groups();
                break;
            case "add_students_to_groups":
                $this->add_students_to_groups();
                break;
            case "cleanup":
                $this->cleanup();
                break;
            default:
                $this->warn("Unknown command ".$command);
        }
    }

    private function find_accounts()
    {
        $connector = new GitLabConnector();

        $users = User::whereNull('gitlab_user_id')->where('role','&', User::student_role)->get();

        foreach($users as $user)
        {
    //        $gu = $connector->find_user_by_username($user->uvanetid);
            $gu = $connector->find_user_by_string($user->name);

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
            if($group->is_dummy())
                continue;

            $gg = $connector->find_group_by_string($group->name);

            if($gg == null)
            {
                $this->info("No match for ". $group->name);

                $gg = $group->create_gitlab_group($connector);
                $this->info("Created a new group with id ".$gg->id);
                continue;
            }

            $this->info("Found ".$gg->name);
            $group->gitlab_group_id = $gg->id;
            $group->save();
        }
    }

    private function add_assistants_to_groups()
    {
        $connector = new GitLabConnector();

        $groups = Group::whereNotNull('gitlab_group_id')->get();

        foreach($groups as $group)
        {

            if($group->assistant_id == null)
            {
                $this->info($group->name . ": assistant unknown");
                continue;
            }

            if($group->assistant->gitlab_user_id == null)
            {
                $this->info($group->name . ": assistant has no gitlab account");
                continue;
            }

            $gg = $group->gitlab_group($connector);

            if(! in_array($group->assistant->gitlab_user_id, $gg->member_user_ids()))
            {
                $gg->add_member($group->assistant->gitlab_user_id, 20);

                $this->info("Added ".$group->assistant->name." as a reporter to ".$gg->name);
                continue;
            }

            $this->info($group->assistant->name." already member of group ".$gg->name);
        }

    }

    private function add_students_to_groups()
    {
        $connector = new GitLabConnector();

        $groups = Group::whereNotNull('gitlab_group_id')->get();

        /** @var Group[] $groups */
        foreach($groups as $group)
        {
            if($group->gitlab_group_id == null)
            {
                $this->info("Group ".$group->name." not created in GitLab");
                continue;
            }

            $gg = $group->gitlab_group($connector);
            $members = $gg->member_user_ids();

            foreach($group->students as $student)
            {
                if($student->user->gitlab_user_id == null)
                {
                    $this->info("Student not created in GitLab: ".$student->user->name);
                    continue;
                }

                if(in_array($student->user->gitlab_user_id, $members))
                    continue;

                $gg->add_member($student->user->gitlab_user_id, 40);

                $this->info("Added ".$student->user->name." to group ". $gg->name);
            }
        }
    }

    private function cleanup()
    {
        $connector = new GitLabConnector();

        $groups = Group::whereNotNull('gitlab_group_id')->get();

        /** @var Group[] $groups */
        foreach($groups as $group)
        {
            $gg = $group->gitlab_group($connector);
            $members = $gg->member_user_ids();

            $gids = $group->students->map(function($s) { return $s->user->gitlab_user_id; })->all();
            if($group->assistant_id != null)
                $gids[] = $group->assistant->gitlab_user_id;


            foreach(array_diff($members, $gids) as $gid)
            {
                $guser = $connector->find_user_by_id($gid);
                if($this->confirm("Do you want to remove ".$guser->name." from ".$gg->name."?"))
                {
                    $this->warn("Removing ".$guser->name);
                    $gg->remove_user($guser->id);
                }
            }

        }
    }
}
