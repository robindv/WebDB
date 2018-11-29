<?php

namespace App\Http\Controllers;

use App\Models\Project;

use App\Models\ServerDomain;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{

    function getProject()
    {
        $group = Auth::user()->student->group;

        $data['projects'] = Project::where('advanced',0)->where('course_id', $group->course_id)->pluck('name','id');
        $data['deadline'] = strtotime(env("WEBDB_PROJECT_DEADLINE"));

        return view('layout')->nest('page','student.project',$data);
    }

    function postProject(Request $request)
    {
        /* Make sure the requesting user has a group */
        $group = Auth::user()->student->group;
        if(!$group)
            return redirect('student/project');

        $project_id = $request->get('project');

        /* When an advanced project has been assigned, no change is possible */
        if(!Project::where('advanced',0)->find($project_id))
            return redirect('student/project');

        $project = $group->project;

        /* Store the change, but only when not chosen or before deadline. */
        if(!$project || !(time() > strtotime(env("WEBDB_PROJECT_DEADLINE")) || $project->advanced))
        {
            $group->project()->associate(Project::find($project_id));
            $group->save();
        }

        return redirect('student/project');
    }

    function getServer()
    {
        return view('layout')->nest('page','student.server');
    }

    function getServerOn()
    {
        $server = Auth::user()->student->group->server;

        if(!$server)
            return;

        $server->start();
        $server->refresh();

        return redirect()->back();
    }
}
