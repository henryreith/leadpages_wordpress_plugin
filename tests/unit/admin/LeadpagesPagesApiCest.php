<?php
namespace admin;

use Mockery as m;
use UnitTester;


class LeadpagesPagesApiCest
{

    public $httpClient;
    public $api;

    public function _before(UnitTester $I)
    {


        $this->httpClient = m::mock('TheLoop\Providers\WordPressHttpClient[setUrl, setArgs, get]');
        $this->api        = m::mock('Leadpages\Admin\Providers\LeadpagesPagesApi[getAccessToken]',
          array($this->httpClient));

    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function getUserPagesTest(UnitTester $I)
    {

        //arrange

        $validPageResponse = include(dirname(dirname(dirname(__FILE__))) . '/_data/getPagesResponse.php');
        $this->httpClient->shouldReceive('setUrl', array('https://my.leadpages.net/page/v1/pages'))
                         ->andSet('url', 'https://my.leadpages.net/page/v1/pages')
                         ->andReturn(true);

        $this->api->shouldReceive('getAccessToken')
                  ->andSet('token', 'leadpagesToken')
                  ->andReturn(true);
        echo $this->api->token;

        $args['headers'] = array(
          'LP-Security-Token' => $this->api->token,
          'timeout'           => 10
        );

        $this->httpClient->shouldReceive('setArgs', array($args))
                         ->andSet('args', $args)
                         ->andReturn(true);

        //act

        $this->httpClient->shouldReceive('get')
                         ->andReturn($validPageResponse);

        $response = $this->api->getUserPages();


        //assert

        $I->assertTrue($this->httpClient->setUrl('https://my.leadpages.net/page/v1/pages'));
        $I->assertEquals('https://my.leadpages.net/page/v1/pages', $this->httpClient->url);
        $I->assertNotNull($this->api->token);




    }
}
