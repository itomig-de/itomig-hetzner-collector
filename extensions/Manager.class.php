<?php

class Manager
{
    protected $client;
    protected $uri = "";

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function list()
    {
        $request = curl_init();

        curl_setopt($request, CURLOPT_URL,$this->client->url().$this->uri);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->client->token()
        ]);

        $serverOutput = curl_exec($request);
        
        curl_close($request);

        return json_decode($serverOutput, true);
    }
}