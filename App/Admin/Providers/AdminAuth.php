<?php

namespace Leadpages\Admin\Providers;

use Leadpages\Helpers\Security;

class AdminAuth
{

    private $api        = '';
    private $token      = '';
    private $security   = '';
    private $username   = '';
    private $password   = '';

    public function __construct(LeadpagesLoginApi $api, Security $security)
    {
        $this->api = $api;
        $this->security = $security;
    }

    public function login()
    {
        add_action( 'admin_post_leadpages_login_form', array($this, 'loginAndRedirect') );

    }

    public function logUserIn(){

        $this->security->userPrivilege('manage_options');

        // Check that nonce field
        $this->security->checkAdminReferer('leadpages_login');

        if ( isset( $_POST['username']) && isset($_POST['password'])  )
        {
            $this->username   = sanitize_email($_POST['username']);
            $this->password   = sanitize_text_field($_POST['password']);
        }

        $response = $this->api->getUserToken($this->username, $this->password);

        return $response;

    }

    public function loginAndRedirect(){

        $response = $this->logUserIn();

        if ($response == 'Created') {
            wp_redirect(admin_url('edit.php?post_type=leadpages_post'));
        } else {
            wp_redirect(admin_url('admin.php?page=Leadpages&error=' . $response));
        }
    }

    public function isLoggedIn()
    {
        $this->token = $this->api->getAccessToken();
        if($this->token != '' || !is_null($this->token)){
            //check if token is still valid
            if($this->api->checkUserToken($this->token)){
                return true;
            }else{
                return false;
            }
        }
    }


}