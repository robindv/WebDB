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
        $group->path = $result->path;
        return $group;
    }

    function find_user_by_id($id)
    {
        $result = $this->do_request("GET", ["users", $id], []);

        if (!isset($result->id))
            return null;

        return $this->result_to_user($result);
    }

    function find_user_by_username($username)
    {
        $result = $this->do_request("GET", ["users"], ["username" => $username]);

        if (! count($result))
            return null;

        return $this->result_to_user($result[0]);
    }

    function find_user_by_string($search)
    {
        $result = $this->do_request("GET", ["users"], ["search" => $search]);

        if (! count($result))
            return null;

        return $this->result_to_user($result[0]);
    }


    private function result_to_user($result)
    {
        $user = new GitlabUser($this);
        $user->id = $result->id;
        $user->name = $result->name;
        $user->email = $result->email;
        $user->username = $result->username;

        return $user;
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
        $group->path = $result->path;
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

    function save_user(GitLabUser $user)
    {
        if($user->id == null)
        {
            $result = $this->do_request("POST", ["users"],
                ['email' => $user->email,
                 'password' => str_random(10),
                 'username' => $user->username,
                 'name' => $user->name,
                 'projects_limit' => 10,
                 'provider' => 'cas3',
                 'extern_uid' => $user->username,
                 'skip_confirmation' => "true"]
            );

            if(isset($result->id))
            {
                $user->id = $result->id;
            }
        }
    }

    function get_member_user_ids_from_group($group_id)
    {
        $result = $this->do_request("GET", ["groups", $group_id, "members"], []);

        return array_map(function($e) { return $e->id; }, $result);
    }

    function add_member_to_group($user_id, $group_id, $access_level)
    {
        $this->do_request("POST", ["groups", $group_id, "members"], ["user_id" => $user_id, "access_level" => $access_level]);
    }

}


class GitLabGroup {

    public $id;
    public $name;
    public $path;
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

    function member_user_ids()
    {
        return $this->connector->get_member_user_ids_from_group($this->id);
    }

    function add_member($user_id, $access_level)
    {
        $this->connector->add_member_to_group($user_id, $this->id, $access_level);
    }
}

class GitLabUser {

    public $id;
    public $name;
    public $username;
    public $email;
    private $connector;

    function __construct(GitLabConnector $connector)
    {
        $this->connector = $connector;
    }

    function save()
    {
        $this->connector->save_user($this);
    }


}
