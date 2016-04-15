<?php

namespace TheLoop\Contracts;


interface HttpClient
{
    public function get();
    public function post();
    public function patch();
    public function delete();
    public function setUrl($url);

}

