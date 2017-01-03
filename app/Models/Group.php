<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    function students()
    {
        return $this->hasMany('App\Models\Student');
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

    function getFullnameAttribute() {
        return env('WEBDB_GROUP_PREFIX') . $this->name;
    }

    function gitlab_group(\App\Connectors\GitLabConnector $connector)
    {
        return $connector->find_group_by_id($this->gitlab_group_id);
    }

    function create_gitlab_group(\App\Connectors\GitLabConnector $connector)
    {
        $gitlab_group = new \App\Connectors\GitLabGroup($connector);
        $gitlab_group->name = $this->fullname;
        $gitlab_group->path = $this->fullname;
        $gitlab_group->save();

        $this->gitlab_group_id = $gitlab_group->id;
        $this->save();

        return $gitlab_group;
    }
}
