<?php


namespace Leadpages\Helpers;


trait LPToken
{
    public function parseTokenResponse($response){
        $data = $response['body'];
        $bodyObj = json_decode($data);
        return $bodyObj->securityToken;
    }

    public function setToken($token){
        $this->token = $token;
    }

    public function setAccessToken() {
        update_option( 'leadpages_security_token', $this->token );
    }

    public function getAccessToken() {
        return get_option( 'leadpages_security_token', false );

    }

    public function deleteAccessToken(){
        delete_option('leadpages_security_token');
    }

}