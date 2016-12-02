<?php

namespace App\Connectors;

class CloudStackConnector
{
    private $apikey;
    private $secretkey;
    private $url;

    function __construct()
    {
        $this->apikey = env('CLOUDSTACK_APIKEY');
        $this->secretkey = env('CLOUDSTACK_SECRETKEY');
        $this->url = env('CLOUDSTACK_URL');
    }

    private function get_signature($params)
    {
        ksort($params);
        $fieldstring = [];
        foreach($params as $key => $value)
            $fieldstring[] = strtolower($key.'='.urlencode($value));
        $fieldstring = implode('&', $fieldstring);

        return urlencode(base64_encode(hash_hmac("sha1", $fieldstring, $this->secretkey, true)));
    }

    private function do_get_request($params)
    {
        $params["response"] = "json";
        $params["apiKey"] = $this->apikey;
        $ch = curl_init();

        $fieldstring = implode('&', array_map(function($key, $value) { return $key.'='.urlencode($value); }, array_keys($params), $params));

        curl_setopt($ch, CURLOPT_URL, $this->url . "?" . $fieldstring . "&signature=" . $this->get_signature($params));
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

    function list_service_offerings()
    {
        return $this->do_get_request([
            "command" => "listServiceOfferings",
            "listAll" => "true"
        ]);
    }

    function list_networks()
    {
        return $this->do_get_request([
            "command" => "listNetworks",
            "listAll" => "true"
        ]);
    }

    function list_zones()
    {
        return $this->do_get_request([
            "command" => "listZones"
        ]);
    }

    function list_templates($filter = "community")
    {
        return $this->do_get_request([
            "command" => "listTemplates",
            "templatefilter" => $filter,
            "listAll" => "true"
        ]);
    }

    function deploy_virtual_machine($name)
    {
        return $this->do_get_request([
            "command" => "deployVirtualMachine",
            "name"    => $name,
            "serviceofferingid" => env('CLOUDSTACK_SERVICEOFFERINGID'),
            "templateid" => env('CLOUDSTACK_TEMPLATEID'),
            "zoneid" => env("CLOUDSTACK_ZONEID"),
            "networkids" => env('CLOUDSTACK_NETWORKID'),
        ]);

        // store id from deployvirtualmachineresponse->id
    }

}
