<?php

namespace App\Http\Controllers\API;


use App\Models\Project;
use Illuminate\Http\Request;

class GroupsController
{

    function getGroup(Request $request)
    {
        $group_id =  $request->user()->student->group_id;

        $group = \App\Models\Group::with('project','assistant','students.user')->find($group_id)->setVisible(['id','name','assistant','project','students','students_can_edit_project','project_id']);

        if($group->project != null)
            $group->project->setVisible(['name']);
        if($group->assistant != null)
            $group->assistant->setVisible(['name']);

        $group->students->map(function($student)
        {
           $student->setVisible(['user']);
           $student->user->setVisible(['name','email']);
        });

        return $group;
    }

    function postProject(Request $request)
    {
        /* Make sure the requesting user has a group */
        $group = $request->user()->student->group;
        if(!$group)
            return response(['error'=> 'Mag niet'], 403);

        $project_id = $request->get('project_id');

        /* When an advanced project has been assigned, no change is possible */
        if(Project::where('advanced',1)->find($project_id))
            return response(['error'=> 'Mag niet'], 403);

        /* Store the change, but only when not chosen or before deadline. */
        if($group->students_can_edit_project)
        {
            $group->project()->associate(Project::find($project_id));
            $group->save();
        }

        return;
    }

    function getGroups(Request $request)
    {
        $user = $request->user();
        $course = current_course();

        if( (!$user->is_assistant && !$user->is_teacher) || ($user->course_id != $course->id && !$user->is_admin))
            return [];

        $query = \App\Models\Group::where('course_id', $course->id);

        if(!$user->is_teacher)
            $query->where('assistant_id', $user->id);

        return $query->with(['project','assistant','students','students.user'])->get()
            ->map(function($group){

                $group->setVisible(['id','assistant','name','project','remark','students','project_id','assistant_id']);

                if($group->assistant != null)
                    $group->assistant->setVisible(['name','email']);

                if($group->project != null)
                    $group->project->setVisible(['id','name']);

                $group->students->map(function($student) {
                    $student->user->setVisible(['name','email']);
                    $student->setVisible(['id','user','remark']);
                    return $student;
                });
                return $group;
            });
    }

    function postGroups(Request $request)
    {
        $user = $request->user();
        $course = current_course();

        if( (!$user->is_assistant && !$user->is_teacher) || ($user->course_id != $course->id && !$user->is_admin))
            return response(['error'=> 'Mag niet'], 403);

        $body = json_decode($request->getContent());

        $data = $request->validate([
            'name' => 'required',
        ]);

        $group = \App\Models\Group::find($body->id);
        $group->remark = $body->remark;
        $group->project_id = $body->project_id;
        $group->assistant_id = $body->assistant_id;

        $group->save();

        return $group;

    }

}