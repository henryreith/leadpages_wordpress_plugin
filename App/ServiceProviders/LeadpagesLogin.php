<?php


namespace Leadpages\ServiceProviders;

use Leadpages\Lib\ApiResponseHandler;
use Leadpages\Lib\Security;
use TheLoop\Contracts\HttpClient;

class LeadpagesLogin
{

    use Security;
    /**
     * @var \TheLoop\Contracts\HttpClient
     */
    private $client;
    private $leadpagesConfig;
    private $token;
    /**
     * @var \Leadpages\Lib\ApiResponseHandler
     */
    private $responseHandler;

    public function __construct(HttpClient $client, ApiResponseHandler $responseHandler, $leadpagesConfig)
    {
        $this->client          = $client;
        $this->leadpagesConfig = $leadpagesConfig;
        $this->responseHandler = $responseHandler;
    }

    public function loginHook()
    {
        add_action('admin_post_leadpages_login_form', array($this, 'logUserIn'));

    }

    public function logUserIn()
    {
        $this->userPrivilege('manage_options');
        $this->checkAdminReferer('leadpages_login');

        if (isset($_POST['username']) && isset($_POST['password'])) {
            $this->username = sanitize_email($_POST['username']);
            $this->password = sanitize_text_field($_POST['password']);
            $response = $this->getUserToken($this->username, $this->password);
            return $response;
        }


    }

    /**
     * Get user token for LeadpagesApi
     *
     * @param $username
     * @param $password
     */
    public function getUserToken($username, $password)
    {
        $this->userPrivilege('manage_options');
        $url             = $this->leadpagesConfig['api']['sessions']['new'];
        $args['headers'] = array(
          'Authorization' => 'Basic ' . base64_encode($username . ':' . $password)
        );

        //set args for http client
        $this->client->setArgs($args);
        //make get request for httpclient
        $response        = $this->client->post($url);
        $responseHandler = $this->responseHandler->checkResponse($response);
        //if response is good(2xx message) store the token and redirect to the leadpages_post page
        if ($responseHandler == 'success') {
            $this->parseTokenResponse($response);
            $this->storeToken();
            \wp_redirect(admin_url('edit.php?post_type=leadpages_post'));
        } else {
            //if anything other than a 2xx response redirect to url where error will display
            \wp_redirect(admin_url('admin.php?page=Leadpages&code='.$responseHandler));
        }

    }

    public function parseTokenResponse($response)
    {
        $data        = $response['body'];
        $bodyObj     = json_decode($data);
        $this->token = $bodyObj->securityToken;
    }


    /**
     * @return bool
     */
    public function isUserLoggedIn()
    {
        $this->token = $this->getAccessToken();
        if ($this->token) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *store users token in database
     */
    public function storeToken()
    {
        update_option('leadpages_security_token', $this->token);
    }

    /**
     * retrieve users token from database
     * @return mixed|void
     */
    public function getAccessToken()
    {
        return get_option('leadpages_security_token', false);
    }

    /**
     *delete users token from database
     */
    public function deleteAccessToken()
    {
        delete_option('leadpages_security_token');
    }
}