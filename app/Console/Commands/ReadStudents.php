<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Group;
use App\Models\Student;

class ReadStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webdb:read_students {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read students from CSV file';

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
        $filename = $this->argument('filename');

        if(! file_exists($filename))
            return $this->error("Could not find file ". $filename);

        $file = trim(file_get_contents($filename));
        $lines = explode("\n", $file);

        if(count($lines) < 2)
            return $this->error("Invalid CSV file (1)");

        /* Parse header */
        $header_line = explode(";", $lines[0]);
        if(count($header_line) < 6)
            return $this->error("Invalid CSV file (2)");

        $mapping = ['UvAnetID' => -1, 'LastName' => -1, 'MiddleName' => -1, 'FirstName' => -1, 'Email' => -1, 'Programme' => -1, 'Groep' => -1, 'Tutor' => -1];

        foreach($header_line as $key=>$value)
            if(array_key_exists($value, $mapping))
                $mapping[$value] = $key;

        if(in_array(-1, $mapping))
            return $this->error("Missing column: ". array_search(-1, $mapping));

        /* Parse line */
        for($i = 1; $i < count($lines); $i++)
        {
            $line = explode(";", $lines[$i]);

            $uvanetid  = $line[$mapping['UvAnetID']];
            $groupname = str_replace(env("WEBDB_GROUP_PREFIX"), "", $line[$mapping['Groep']]);

            $group = Group::where('name', $groupname)->first();
            if($group == null)
            {
                $this->info('Group '.$groupname.' does not exists');
            }

            $user = User::where('uvanetid', $uvanetid)->first();

            if($group == null && $user == null)
            {
                $this->info('Skipping '. $uvanetid);
                continue;
            }

            if($user == null)
            {
                $user = new User();
                $user->uvanetid = $uvanetid;
                $this->info('Creating user '.$uvanetid);
            }

            $user->firstname = $line[$mapping['FirstName']];
            $user->infix     = $line[$mapping['MiddleName']];
            $user->lastname  = $line[$mapping['LastName']];
            $user->email     = $line[$mapping['Email']];
            $user->role     |= User::student_role;
            $user->save();

            $student = $user->student;

            if($student == null)
            {
                $student = new Student();
                $student->user_id = $user->id;
                $this->info('Creating student '.$uvanetid);
            }

            $student->programme = $line[$mapping['Programme']];

            $old_group = $student->group_id;
            $old_active = $student->active;
            if($group == null)
            {
                $student->group_id = null;
                $student->active   = 0;
            }
            else
            {
                $student->group_id = $group->id;
                $student->active   = 1;
            }

            if($old_group != $student->group_id)
                $this->warn("Changing group of ". $student->user->name);
            if($old_active != $student->active)
                $this->info("Changing active status of ". $student->user->name);

            /* Tutor */
            $tutor = User::whereRaw("CONCAT(firstname,' ',TRIM(CONCAT(infix,' ',lastname))) = ?",[$line[$mapping['Tutor']]])->first();

            if($tutor == null)
                $this->info('Tutor not found: '. $line[$mapping['Tutor']]);
            $student->tutor_id = $tutor == null ? null : $tutor->id;

            $student->save();
        }

    }
}
