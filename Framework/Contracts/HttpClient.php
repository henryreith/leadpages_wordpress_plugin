<?php
/**
 * Created by PhpStorm.
 * User: brand
 * Date: 3/22/2016
 * Time: 11:43 PM
 */

namespace TheLoop\Contracts;


interface HttpClient
{
    public function get();
    public function post();
    public function patch();
    public function delete();
}

