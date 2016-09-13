<?php

namespace LeadpagesWP\ServiceProviders;

use Leadpages\Auth\LeadpagesLogin;
use LeadpagesMetrics\LeadpagesSignInEvent;

class WordPressLeadpagesAuth extends LeadpagesLogin
{

    public static function getName()
    {
        return get_called_class();
    }

    /**
     * method to implement on extending class to store token in database
     *
     * @return mixed
     */
    public function storeToken()
    {
        update_option($this->tokenLabel, $this->token);
    }

    /**
     * method to implement on extending class to get token from datastore
     * should return token not set property of $this->token
     * @return mixed
     */
    public function getToken()
    {
        $this->token = get_option('leadpages_security_token');
    }

    /**
     * method to implement on extending class to remove token from database
     * @return mixed
     */
    public function deleteToken()
    {
        delete_option($this->tokenLabel);
    }


    public function login()
    {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = stripslashes($_POST['password']); //wordpress automaticlly escapes ' so if the password has one login fails
            $response = $this->getUser($username, $password)->parseResponse();
            return $response;
        }
    }

    public function redirectOnLogin()
    {
        $response = $this->login();
        if ($response == 'success') {
            $this->storeToken();
            $this->setLoggedInCookie();
            $eventArray = array(
              'email_address' => $_POST['username']
            );
            (new LeadpagesSignInEvent())->storeEvent($eventArray);
            wp_redirect(admin_url('edit.php?post_type=leadpages_post'));
            exit;
        } else {
            //redirect with error code to display error message
            $response = json_decode($response, true);
            $code = sanitize_text_field($response['code']);
            wp_redirect(admin_url('admin.php?page=Leadpages&code='.$code.''));
            exit;
        }
    }


    public function loginHook()
    {
        add_action('admin_post_leadpages_login_form', array($this, 'redirectOnLogin'));
    }

    /**
     * method to check if token is empty
     *
     * @return mixed
     */
    public function checkIfTokenIsEmpty()
    {
        $this->getToken();

        if(empty($this->token)){
            return [
              'code'     => '500',
              'response' => 'token not set in database',
              'error'    => (bool)true
            ];
        }
        else{
            return [
              'code'     => '200',
              'response' => '',
              'error'    => (bool)false
            ];
        }
    }

    /**
     * Check if user is logged in
     * @return bool
     */
    public function isLoggedIn()
    {

        //if cookie is set and is true don't bother with http call
        //if($this->getLoggedInCookie()) return true;

        $isTokenEmpty = $this->checkIfTokenIsEmpty();
        //verify that token in database was not empty, and ensure that token gets a response from Leadpages
        if ($isTokenEmpty['error']) {
            return false;
        }
        //set cookie if they are logged in
        //$this->setLoggedInCookie();

        return true;
    }

    /**
     * Set logged in cookie if it is not already set
     */
    public function setLoggedInCookie()
    {
        if(!$this->getLoggedInCookie()) {
            setcookie('LeadpagesWordPress', true, time() + 3600);
        }
    }

    /**
     * Attempt to fetch login cookie for Leadpages
     * @return bool
     */
    public function getLoggedInCookie()
    {
        if(isset($_COOKIE['LeadpagesWordPress']) && $_COOKIE['LeadpagesWordPress'] == true){
            return true;
        }else{
            return false;
        }
    }
}