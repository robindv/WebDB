<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;

class StudentsController
{


    function getStudents(Request $request)
    {
        $user = $request->user();
        $course = current_course();

        if( (!$user->is_assistant && !$user->is_teacher) || ($user->course_id != $course->id && !$user->is_admin))
            return [];


        $query = \App\Models\Student::with('user','group')
            ->whereHas('user', function($q) use ($course) { return $q->where('course_id', $course->id);});

        if($user->is_assistant && ! $user->is_teacher)
            $query = $query->whereHas('group', function($q) use ($user){ return $q->where('assistant_id', $user->id); });

        return $query->get()->map(function ($student) {
            if($student->group != null)
                $student->group->setVisible(['name','remark']);
            if($student->user != null)
                $student->user->setVisible(['uvanetid','name','email']);

            return $student->setVisible(['id','active','programme','remark','group_id','group','user']);
        });
    }

    function postStudents(Request $request)
    {
        $user = $request->user();
        $course = current_course();

        if( (!$user->is_assistant && !$user->is_teacher) || ($user->course_id != $course->id && !$user->is_admin))
            return response("Verboden", 403);

        $body = json_decode($request->getContent());

        $data = $request->validate([
            'user.email' => 'required',
            'programme' => 'required',
        ]);

        $student = \App\Models\Student::find($body->id);

        $student->remark = $body->remark;

        if($user->is_teacher) {
            $student->programme = $body->programme;
            $student->active = $body->active;
            $student->group_id = $body->group_id;
            $student->user->email = $body->user->email;
            $student->user->save();
        }
        $student->save();

        return $student;
    }

}