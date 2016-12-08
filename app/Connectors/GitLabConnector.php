<?php

namespace App\Connectors;

class GitLabConnector
{
    private $apiurl;
    private $privatetoken;

    function __construct()
    {
        $this->apiurl       = env('GITLAB_APIURL');
        $this->privatetoken = env('GITLAB_PRIVATETOKEN');
    }

    private function do_request($type, $segments, $params)
    {
        $ch = curl_init();

        $fieldstring = implode('&', array_map(function($key, $value) { return $key.'='.urlencode($value); }, array_keys($params), $params));

        curl_setopt($ch, CURLOPT_URL, $this->apiurl . "/" . implode("/", $segments));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["PRIVATE-TOKEN: ". $this->privatetoken]);
        $result = curl_exec($ch);

        return json_decode($result);
    }

    function get_api_user()
    {
        $result = $this->do_request("GET",["user"], []);


        $user = new GitLabUser($this);
        $user->id = $result->id;
        $user->name = $result->name;

        return $user;
    }

    function find_group_by_id($id)
    {
        $result = $this->do_request("GET",["groups", $id], []);

        if(!isset($result->id))
            return null;

        $group = new GitLabGroup($this);
        $group->id = $result->id;
        $group->name = $result->name;
        return $group;
    }

    function find_group_by_string($search)
    {
        $result = $this->do_request("GET",["groups"], ['search'=>$search]);

        if(count($result) != 1)
            return null;

        $result = $result[0];

        $group = new GitLabGroup($this);
        $group->id = $result->id;
        $group->name = $result->name;
        return $group;
    }

    function remove_user_from_group($group_id, $user_id)
    {
        /* TODO: only downgrade when necessary */
        $this->do_request("PUT",["groups", $group_id, "members", $user_id],["access_level" => 10]);
        $this->do_request("DELETE", ["groups", $group_id, "members", $user_id], []);
    }

    function save_group(GitLabGroup $group)
    {
        /* Create */
        if($group->id == null)
        {

            $result = $this->do_request("POST", ["groups"],
                ['name' => $group->name, 'path' => $group->path,
                'visibility_level' => 0]);

            if(isset($result->id))
            {
                $group->id = $result->id;
                /* Strangely enough, the API user gets automatically added, remove.. */
                $this->remove_user_from_group($group->id, $this->get_api_user()->id);
            }
        }
        return;
    }

}


class GitLabGroup {

    public $id;
    public $name;
    private $connector;

    function __construct(GitLabConnector $connector)
    {
        $this->connector = $connector;
    }

    function save()
    {
        $this->connector->save_group($this);
    }

    function remove_user($user_id)
    {
        $this->connector->remove_user_from_group($this->id, $user_id);
    }
}

class GitLabUser {

    public $id;
    public $name;
    private $connector;

    function __construct(GitLabConnector $connector)
    {
        $this->connector = $connector;
    }


}
