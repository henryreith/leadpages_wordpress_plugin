<?php


namespace Leadpages\Admin\Providers;

use Leadpages\helpers\LPToken;
use Mockery\CountValidator\Exception;
use TheLoop\Contracts\HttpClient;

class LeadpagesPagesApi
{
    use LPToken;

    protected $allPagesUrl = '';
    protected $getPageUrl = '';
    protected $client = '';
    public $token = null;

    public function __construct(HttpClient $client)
    {
        global $config;
        $this->client      = $client;
        $this->PagesUrl = $config['api']['pages'];
    }

    public function getUserPages($cursor = false)
    {
        //try to set user token(should be done in constructor but test wont allow it
        try {
            if (is_null($this->token)) {
                $this->token = $this->getAccessToken();
            }
        }catch(Exception $e){
            echo $e->getMessage();
        }



        if(!$cursor) {
            $url = $this->PagesUrl;
        }else{
            $url = $this->PagesUrl . '?cursor='.$cursor;
        }
        $this->client->setUrl($url);
        $args            = array();
        $args['headers'] = array(
          'LP-Security-Token' => $this->token,
          'timeout' => 10
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

    public function getAllUserPages($returnResponse = array(), $cursor = false){

        $response = $this->getUserPages($cursor);

        if($response['_meta']['hasMore'] == true){
            $returnResponse[] = $response['_items'];
            return $this->getAllUserPages($returnResponse, $response['_meta']['nextCursor']);
        }

        if (!$response['_meta']['hasMore']) {
            /**
             * add last result to return response
             */
            $returnResponse[] = $response['_items'];

            /**
             * this maybe a bit hacky but for recursive and compatibility with other functions
             * needed all items to be under one array under _items array
             */
            //echo '<pre>';print_r($returnResponse);die();

            if (isset($returnResponse) && sizeof($returnResponse) > 0) {
                $pages = array(
                  '_items' => array()
                );
                foreach ($returnResponse as $subarray) {
                    $pages['_items'] = array_merge($pages['_items'], $subarray);
                }
                //echo '<pre>';print_r($pages);die();
                return $pages;
            }
        }
    }

    public function stripB3NonPublished()
    {
        $pages = $this->getAllUserPages();
        //echo '<pre>'; print_r($pages);die();

        //unset index if b3 page is not published
        foreach($pages['_items'] as $index => $page){
            if($page['isBuilderThreePage'] && !$page['isBuilderThreePublished']){
                unset($pages['_items'][$index]);
            }
        }

        return $pages;
    }

    public function getSinglePage($pageId){
        $this->client->setUrl($this->PagesUrl.'/'.$pageId);
        $args            = array();
        $args['headers'] = array(
          'LP-Security-Token' => $this->token,
          'timeout' => 10
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
        try {
            if (is_null($this->token)) {
                $this->token = $this->getAccessToken();
            }
        }catch(Exception $e){
            echo $e->getMessage();
        }

        $data = $this->getSinglePage($pageId);
        $data = $data['_meta'];
        $pageUrl = $data['publishUrl'];
        $this->client->setUrl($pageUrl);
        $html = $this->client->get();
        return $html['body'];
    }

}