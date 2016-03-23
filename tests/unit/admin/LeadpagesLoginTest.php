<?php
namespace admin;

use Leadpages\Admin\Providers\AdminAuth;
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
    }

    protected function _after()
    {
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


        $auth   = new AdminAuth($this->api, $this->security);
        $result = $auth->logUserIn();

        $this->assertEquals($result, 'Created');
    }


   /* public function testIsLoggedIn(){

        //todo set this->token and actually check that

        $this->api->shouldReceive('getAccessToken')
            ->once()
            ->andReturn('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiJsZWFkcGFnZXMubmV0IiwiaXNzIjoiYXBpLmxlYWRwYWdlcy5pbyIsImFjY2Vzc0lkIjoiZ1Z0RUE4ZXZXTG8yZDdxZnd3MmljUCIsInNlc3Npb25JZCI6IkRTRUVVTlI5Y1JiN2ttVW5Bc3htY0oiLCJleHAiOjE0NjA2NjQwNTcsImlhdCI6MTQ1ODA3MjA1N30.u6dsNmj9A3I0T6gdgjcV9B5m4lCY1jfmhmTH9NMdtPc');

        $this->api->shouldreceive('checkUserToken')
             ->once()
             ->andReturn(true);

    }*/
}