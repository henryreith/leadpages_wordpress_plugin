<?php


namespace Leadpages\Admin\Providers;

use Leadpages\helpers\LPToken;
use TheLoop\Contracts\HttpClient;

class LeadpagesPagesApi
{
    use LPToken;

    protected $allPagesUrl = '';
    protected $getPageUrl = '';
    protected $client = '';
    protected $token;

    public function __construct(HttpClient $client)
    {
        global $config;
        $this->client      = $client;
        $this->PagesUrl = $config['api']['pages'];
        $this->token       = $this->getAccessToken();
    }

    public function getAllUserPages()
    {

        $this->client->setUrl($this->PagesUrl);
        $args            = array();
        $args['headers'] = array(
          'LP-Security-Token' => $this->token
        );
        $this->client->setArgs($args);
        $response        = $this->client->get();
        $code            = $this->client->getResponseCode($response);
        if ($code > 299 || $code == 'error') {
            return __('Error getting Pages. Please try again later',
              'leadpages');
        }
        return $this->client->getBody($response);
    }

    public function getSinglePage($pageId){
        $this->client->setUrl($this->PagesUrl.'/'.$pageId);
        $args            = array();
        $args['headers'] = array(
          'LP-Security-Token' => $this->token
        );
        $this->client->setArgs($args);
        $response        = $this->client->get();
        $code            = $this->client->getResponseCode($response);
        if ($code > 299 || $code == 'error') {
            return __('Error getting Pages. Please try again later',
              'leadpages');
        }
        return $this->client->getBody($response);
    }

    public function downloadPageHtml($pageId)
    {
        //TODO Janky way to do this but will work for now till APIS are updated
        $data = $this->getSinglePage($pageId);
        $data = $data['_meta'];
        $pageUrl = $data['publishUrl'];
        $this->client->setUrl($pageUrl);
        $html = $this->client->get();
        return $html['body'];
    }

}