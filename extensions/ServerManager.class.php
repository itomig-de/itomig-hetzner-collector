<?php

class ServerManager extends Manager
{
    public function __construct(Client $client)
    {
        parent::__construct($client);
        $this->uri = "servers";
    }
}