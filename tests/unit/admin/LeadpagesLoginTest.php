<?php
namespace admin;

use Leadpages\Admin\Providers\AdminAuth;
use Leadpages\Helpers\LPToken;
use Mockery as m;

class testLeadpagesLogin extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */

    protected $api;
    protected $tester;
    protected $security;

    protected function _before()
    {
        $this->api      = m::mock('Leadpages\Admin\Providers\LeadpagesLoginApi');
        $this->security = $security = m::mock('Leadpages\Helpers\Security');
        $this->auth     = new AdminAuth($this->api, $this->security);
    }

    protected function _after()
    {
        m::close();
    }

    // tests



    public function testLoginSuccess()
    {


        $this->api->shouldReceive('getUserToken')
            ->once()
            ->andReturn('Created');


        $this->security->shouldReceive('userPrivilege')
                 ->once()
                 ->andReturn(true);

        $this->security->shouldReceive('checkAdminReferer')
                 ->once()
                 ->andReturn(true);

        $result = $this->auth->logUserIn();

        $this->assertEquals($result, 'Created');
    }


    public function testIsLoggedInSuccess(){

        //i feel this test is terrible as I am telling checkuserToken to return
        //true and not actually chekcing the value of the access tokens

        $this->api->accessToken = $this->api->shouldReceive('getAccessToken')
            ->once()
           // ->andSet('accessToken', '123abc')
            ->andReturn('123abc');

        $this->api->shouldreceive('checkUserToken')
                  ->with('123abc')
                  ->once()
                  ->andReturn(true);


        $result = $this->auth->isLoggedIn();

        $this->assertEquals($result, true);

    }

    public function testIsLoggedInFail(){

        //i feel this test is terrible as I am telling checkuserToken to return
        //true and not actually chekcing the value of the access tokens

        $this->api->accessToken = $this->api->shouldReceive('getAccessToken')
                    ->once()
                    ->andSet('accessToken', '123abc')
                    ->andReturn('123abc');

        $this->api->shouldreceive('checkUserToken')
                  ->with('123abc') //for some reason this needs to be set to the return value of getAccessToken?
                  ->once()
                  ->andReturn(false);


        $result = $this->auth->isLoggedIn();

        $this->assertEquals($result, false);

    }
}