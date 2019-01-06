<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $appends = ['students_can_edit_project'];

    function getStudentsCanEditProjectAttribute()
    {
        if($this->project == null)
            return true;

        if($this->project->advanced)
            return false;

        return $this->course->project_deadline->gt(Carbon::now());
    }

    function course()
    {
        return $this->belongsTo(Course::class);
    }

    function students()
    {
        return $this->hasMany('App\Models\Student');
    }

    function is_dummy()
    {
        return strstr($this->name, "voorbeeld");
    }

    function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    function assistant()
    {
        return $this->belongsTo('App\Models\User');
    }

    function server()
    {
        return $this->hasOne('App\Models\Server');
    }

    function gitlab_group(\App\Connectors\GitLabConnector $connector)
    {
        return $connector->find_group_by_id($this->gitlab_group_id);
    }

    function create_gitlab_group(\App\Connectors\GitLabConnector $connector)
    {
        $gitlab_group = new \App\Connectors\GitLabGroup($connector);
        $gitlab_group->name = $this->name;
        $gitlab_group->path = $this->name;
        $gitlab_group->save();

        $this->gitlab_group_id = $gitlab_group->id;
        $this->save();

        return $gitlab_group;
    }
}
