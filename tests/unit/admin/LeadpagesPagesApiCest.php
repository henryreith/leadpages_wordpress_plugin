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
    public function getUserPagesTestSuccess(UnitTester $I)
    {
        $I->wantTo('Make sure that getUserPages returns a proper response');


        //arrange

        $validPageResponse = include(dirname(dirname(dirname(__FILE__))) . '/_data/getPagesResponse.php');
        $this->httpClient->shouldReceive('setUrl', array('https://my.leadpages.net/page/v1/pages'))
                         ->andSet('url', 'https://my.leadpages.net/page/v1/pages')
                         ->andReturn(true);

        $this->api->shouldReceive('getAccessToken')
                  ->once()
                  ->andSet('token', 'LeadpagesToken')
                  ->andReturn('LeadpagesToken');


        $args['headers'] = array(
          'LP-Security-Token' => $this->api->token,
          'timeout'           => 10
        );

        $this->httpClient->shouldReceive('setArgs', array($args))
                         ->andSet('args', $args)
                         ->andReturn(true);

        $this->httpClient->shouldReceive('get')
                         ->andReturn($validPageResponse);
        //act


        $response = $this->api->getUserPages();
        //echo '<pre>';print_r($response);die();


        //assert

        $I->assertTrue($this->httpClient->setUrl('https://my.leadpages.net/page/v1/pages'));
        $I->assertEquals('https://my.leadpages.net/page/v1/pages', $this->httpClient->url);
        $I->assertNotNull($this->api->token);
        $I->assertNotEmpty($this->api->token);
        //assert that you can find items
        $I->assertEquals(1, array_key_exists('_items', $response), "Could not find _items in array");
        //assert that the # of items is > 0
        $I->assertTrue(sizeof($response['_items']) > 0);


    }
}
