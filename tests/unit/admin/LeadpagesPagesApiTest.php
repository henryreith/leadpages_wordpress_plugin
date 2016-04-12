<?php
namespace admin;

use Leadpages\Admin\Providers\LeadpagesPagesApi;
use Mockery as m;

class LeadpagesPagesApiTest extends \PHPUnit_Framework_TestCase
{

    public $httpClient;
    public $api;

    protected function setUp()
    {

        /**
         * mock HttpClient
         */
        $this->httpClient = m::mock('TheLoop\Contracts\HttpClient');

        $this->api->shouldReceive('getAccessToken')
                  ->once()
                  ->andReturn('LP-Security-Token');

        $this->api        = new LeadpagesPagesApi($this->httpClient);

    }

    protected function tearDown()
    {
    }

    // tests
    public function testMe()
    {
    }
}
