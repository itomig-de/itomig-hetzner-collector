<?php

class HetznerClient
{
    private $url = "https://api.hetzner.cloud/v1/"; //TODO: Lucie: Make it configurable
    private $token = "";

    public function __construct($token)
    {
        if ($token == "") return false;
        $this->token = $token;
    }

    public function url()
    {
        return $this->url;
    }

    public function token()
    {
        return $this->token;
    }

    public function get($attribute)
    {
        switch($attribute)
        {
            case 'server' :
                return new ServerManager($this);
        }
    }

    public function __get($attribute)
    {
        return $this->get($attribute);
    }
}