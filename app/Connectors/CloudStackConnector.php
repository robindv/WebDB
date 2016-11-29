<?php

namespace App\Connectors;

class CloudStackConnector
{
    private $user;
    private $password;
    private $domain;
    private $url;

    /* After session has been established */
    private $sessionkey;
    private $userid;
    private $domainid;
    private $sessionid;
    private $expires;

    function __construct()
    {
        $this->user = env('CLOUDSTACK_USER');
        $this->password = env('CLOUDSTACK_PASSWORD');
        $this->domain = env('CLOUDSTACK_DOMAIN');
        $this->url = env('CLOUDSTACK_URL');

        $this->load_sessionfile();

        if($this->sessionkey == null)
        {
            $this->get_sessionkey();
            $this->save_sessionfile();
        }

    }

    private function load_sessionfile()
    {
         $data = unserialize(file_get_contents(env('CLOUDSTACK_SESSION_FILE')));

         if($data['expires'] > time())
         {
             $this->sessionkey = $data['sessionkey'];
             $this->userid = $data['userid'];
             $this->domainid = $data['domainid'];
             $this->sessionid = $data['sessionid'];
             $this->expires = $data['expires'];
         }
    }

    private function save_sessionfile()
    {
        $data = ['sessionkey' => $this->sessionkey,
                 'userid' => $this->userid,
                 'domainid' => $this->domainid,
                 'sessionid' => $this->sessionid,
                 'expires' => $this->expires];

         file_put_contents(env('CLOUDSTACK_SESSION_FILE'), serialize($data));
    }

    private function get_sessionkey()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);

        $fields = ["command" => "login",
                   "domain" => "/" . $this->domain,
                   "password" => $this->password,
                   "username" => $this->user,
                   "response" => 'json'];

        $fieldstring = [];
        foreach($fields as $key => $value)
            $fieldstring[] = $key.'='.urlencode($value);
        $fieldstring = implode('&', $fieldstring);

        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldstring);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        $header = substr($response, 0, $header_size);
        $header = explode("\r\n",$header);

        foreach($header as $line)
        {
            if(preg_match("/Set-Cookie: JSESSIONID=(.*);/", $line, $output_array))
            {
                $this->sessionid = $output_array[1];
            }
        }


        $result = json_decode(substr($response, $header_size));
        $this->sessionkey = $result->loginresponse->sessionkey;
        $this->userid = $result->loginresponse->userid;
        $this->domainid = $result->loginresponse->domainid;
        $this->expires = time() + $result->loginresponse->timeout - 30;
    }

    private function do_get_request($params)
    {
        $params["response"] = "json";
        $ch = curl_init();

        $fieldstring = [];
        foreach($params as $key => $value)
            $fieldstring[] = $key.'='.urlencode($value);
        $fieldstring = implode('&', $fieldstring);

        $cookiestring = "sessionkey=".$this->sessionkey. "; JSESSIONID=".$this->sessionid;

        curl_setopt($ch, CURLOPT_URL, $this->url . "?" . $fieldstring);
        curl_setopt($ch, CURLOPT_COOKIE, $cookiestring);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);

        return json_decode($result);
    }

    function get_virtual_machine_info($vm_id)
    {
        $response = $this->do_get_request([
            "command" => "listVirtualMachines",
            "listAll" => "true",
            "id"      => $vm_id
        ]);

        if($response->listvirtualmachinesresponse->count != 1)
            return null;

        return $response->listvirtualmachinesresponse->virtualmachine[0];
    }

    function list_virtual_machines()
    {
        return $this->do_get_request([
            "command" => "listVirtualMachines",
            "listAll" => "true"
        ]);
    }

    function stop_virtual_machine($vm_id)
    {
        $response = $this->do_get_request([
            "command" => "stopVirtualMachine",
            "id"      => $vm_id
        ]);
    }

    function start_virtual_machine($vm_id)
    {
        $response = $this->do_get_request([
            "command" => "startVirtualMachine",
            "id"      => $vm_id
        ]);
    }

}
