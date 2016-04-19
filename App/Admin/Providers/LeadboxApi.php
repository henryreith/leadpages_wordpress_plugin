<?php


namespace Leadpages\Admin\Providers;

use Leadpages\Helpers\LPToken;
use TheLoop\Contracts\HttpClient;

class LeadboxApi
{

    use LPToken;
    /**
     * @var \TheLoop\Contracts\HttpClient
     */
    private $client;
    private $token;

    public function __construct(HttpClient $client)
    {

        $this->client = $client;
    }

    public function getLeadBoxes()
    {
        global $config;

        $this->token = $this->getAccessToken();

        $this->client->setUrl($config['api']['leadboxes']);
        $args['headers'] = array(
          'LP-Security-Token' => $this->token,
        );
        $args['timeout'] = 10;
        $this->client->setArgs($args);
        $response = $this->client->get();
        if (is_wp_error($response)) {
            $error_string = $response->get_error_message();
            echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
            exit();
        }
        $body = $this->client->getBody($response);

        foreach ($body['_items'] as $index => $result) {

            //if embed is not set it is not published so it must be removed
            if (empty($result['publish_settings']['embed'])) {
                unset($body['_items'][$index]);
                continue;
            }
            $body['_items'][$index]['publish_settings']['embed'] = htmlentities($result['publish_settings']['embed']);

        }

        return $body;
    }

    public function getSingleLeadbox($id)
    {
        global $config;

        $this->token = $this->getAccessToken();

        $this->client->setUrl($config['api']['leadboxes']."/{$id}");
        $args['headers'] = array(
          'LP-Security-Token' => $this->token,
          'timeout'           => 10
        );
        $this->client->setArgs($args);

        $response = $this->client->get();
        if (is_wp_error($response)) {
            $error_string = $response->get_error_message();
            echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
            exit();
        }
        $body = $this->client->getBody($response);
        return $body['leadbox']['publish_settings']['embed'];


    }

}