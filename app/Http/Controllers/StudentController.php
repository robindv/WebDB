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
        $data['projects'] = Project::where('advanced',0)->pluck('name','id');

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

        /* Store the change, but only when not chosen or before 04-01-2016 18:00 */
        if(!$project || !(time() > 1451926800 || $project->advanced))
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

    function getDomainAddModal()
    {
        $server = Auth::user()->student->group->server;

        if(!$server)
            return;

        return $this->modal('Domein toevoegen', 'modals.domain_add', ['server' => $server]);
    }

    function postDomainAddModal(Request $request)
    {
        $server = Auth::user()->student->group->server;

        if(!$server)
            return;

        /* The domain must not be empty, or already registered, or be invalid */
        $validator = Validator::make(
            ['domain' => $request->get('domain')],
            ['domain' => ['required','unique:server_domains,domain','serverdomain']],
            ["domain.required" => '"Domeinnaam" is een verplicht veld.',
                "unique"       => "Deze domeinnaam is reeds in gebruik.",
                "serverdomain" => "Deze domeinnaam is ongeldig."]
        );

        /* Return back when validation fails */
        if($validator->fails())
            return redirect('student/domain-add-modal')->withErrors($validator)->withInput();

        /* Otherwise add to database */
        $domain = new ServerDomain();
        $domain->server_id = $server->id;
        $domain->locked = 0;
        $domain->ssl = 0;
        $domain->domain = $request->get('domain');
        $domain->save();
    }

    function getDomainDeleteModal(ServerDomain $domain)
    {
        /* Check that the domain belongs to the groups server */
        if(Auth::user()->student->group->id != $domain->server->group->id)
            return;

        /* Students cannot change when the domain is locked */
        if($domain->locked)
            return;

        return $this->modal('Domein verwijderen','modals.domain_delete',['domain' => $domain],'Verwijderen','danger');
    }

    function postDomainDeleteModal(ServerDomain $domain)
    {
        /* Check that the domain belongs to the groups server */
        if(Auth::user()->student->group->id != $domain->server->group->id)
            return;

        /* Students cannot change when the domain is locked */
        if($domain->locked)
            return;

        $domain->delete();
    }
}
