<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Project;
use App\Models\Server;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{

    function getPorts()
    {
        $data['ports'] = json_decode(file_get_contents('http://'.env("WEBDB_API").'/port/'));

        usort($data['ports'], function($a, $b){ return strcmp($a->ListenPort, $b->ListenPort); });

        return view('layout')->nest('page','staff.ports',$data);
    }

    function getGroups()
    {
        $data['groups'] = Group::orderBy('id')->with('students','students.user','project','assistant')->get();

        return view('layout')->nest('page','staff.groups',$data);
    }

    function getGroupsExport()
    {
        $data['groups'] = Group::orderBy('id')->with('students','students.user','project','assistant')->get();

        $headers = ['Content-Type' => 'text/csv','content-disposition' => 'attachment'];

        return response(view('staff.groups_export',$data),200, $headers);
    }

    function getServers()
    {
        $data['servers'] = Server::orderBy('name')->with('group')->get();

        return view('layout')->nest('page','staff.servers',$data);
    }

    function getServer(Server $server)
    {
        $server->refresh();

        return view('layout')->nest('page','staff.server',['server' => $server]);
    }

    function getServerOn(Server $server)
    {
        $server->start();
        $server->refresh();

        return redirect()->back();
    }

    function getServerOff(Server $server)
    {
        $server->stop();

        return redirect()->back();
    }

    function getStudents()
    {
        $data['users'] = User::with('student','student.group')->orderBy('lastname')->get();
        return view('layout')->nest('page','staff.students',$data);
    }

    function getStudentsExport()
    {
        $data['users'] = User::with('student','student.group')->orderBy('lastname')->get();

        $headers = ['Content-Type' => 'text/csv','content-disposition' => 'attachment'];

        return response(view('staff.students_export', $data), 200, $headers);
    }

    function getStudentModal(Student $student)
    {
        $data['student'] = $student;
        $data['groups'] = [0=>''] + Group::orderBy('id')->pluck('name','id')->all();

        return $this->modal('Student bewerken', 'modals.student',$data);
    }

    function postStudentModal(Request $request, Student $student)
    {
        $student->user->email = $request->get('email');
        $student->user->save();

        $student->programme = $request->get('programme');
        $student->active = $request->get('active');
        $student->group_id = $request->get('group_id') ?: null;
        $student->remark = $request->get('remark');
        $student->save();
    }

    function getGroupModal(Group $group)
    {
        $data['group'] = $group;
        $data['assistants'] = [0 => ''];

        $users = User::isAssistant()->orderBy('lastname')->get();
        foreach($users as $user)
            $data['assistants'][$user->id] = $user->name;

        $data['projects'] = [0=>'Onbekend'] + Project::orderBy('name')->pluck('name','id')->all();

        return $this->modal('Groep bewerken', 'modals.group',$data);
    }

    function postGroupModal(Request $request, Group $group)
    {
        /* Check the input */
        $validator = Validator::make($request->all(), ['name'=>'required']);

        if($validator->fails())
        {
            return redirect('staff/group-modal/'.$group->id)->withErrors($validator);
        }

        /* Store changes into database */
        $group->name = $request->get('name');
        $group->assistant_id = $request->get('assistant_id') ?: null;
        $group->project_id = $request->get('project_id') ?: null;
        $group->remark = $request->get('remark');

        $group->save();

        return;
    }
}
