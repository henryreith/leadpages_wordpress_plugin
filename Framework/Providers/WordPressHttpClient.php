<?php


namespace TheLoop\Providers;

use TheLoop\Contracts\HttpClient;

class WordPressHttpClient implements HttpClient
{

    protected $url;
    protected $args =array();

    public function get()
    {
        $response = wp_remote_get($this->url, $this->args);
        return $response;
    }

    public function post()
    {
        $response = wp_remote_post($this->url, $this->args);
        return $response;
    }

    public function patch()
    {
        // TODO: Implement patch() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }


    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param array $args
     */
    public function setArgs($args)
    {
        $this->args = $args;
    }

    public function getArgs(){
        return $this->args;
    }

    public function getResponseCode($response)
    {
        if(isset($response) && is_array($response)){
            $code = NULL;
            array_walk_recursive($response, function($value, $key) use(&$code){
                if($key == 'code'){
                    $code = strval($value);
                }
            });
            if($code == NULL){
                return 'error';
            }
            return $code;
        }
    }

    public function getBody($response){
        if(isset($response) && is_array($response)){
            $body = NULL;
            array_walk_recursive($response, function($value, $key) use(&$body){
                if($key == 'body'){
                    if(! is_array($value)){
                        $body = json_decode($value, true); //return as an array
                    }
                }
            });
            return $body;
        }
    }

}