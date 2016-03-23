<?php


namespace App;

use TheLoop\Contracts\HttpClient;

class FakeApiClass
{
    protected $client;
    public function __construct(HttpClient $client)
    {
        $this->client = $client;
        $this->client->get();
    }

}