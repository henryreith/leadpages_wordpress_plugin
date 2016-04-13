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
        $token = get_option( 'leadpages_security_token', false );
        if(is_null($token) || empty($token) || !isset($token)){
            throw New \Exception("Token is blank, you may need to log back in");
        }
        return $token;
    }

}