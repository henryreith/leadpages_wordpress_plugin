<?php

use Leadpages\ServiceProviders\LeadpagesLogin;

class Test_LeadpagesLogin_Success extends WP_UnitTestCase
{

    public $leadpagesLogin;

    public function setUp()
    {
        parent::setUp();
        //setup leadpages login object
        //remind app is setup in parent::setUp();
        $this->leadpagesLogin = $this->app['leadpagesLogin'];
        $this->assertInstanceOf(LeadpagesLogin::class, $this->leadpagesLogin);

    }

    public function tearDown()
    {
        parent::tearDown();
        //unset token after every test so it can be retested
        unset($this->leadpagesLogin->token);
    }

    /**
     * Get the users token and ensure that it is set in the response and get set in the database properly
     *
     * @group login_success
     */

    public function test_get_user_token_success()
    {
        // call leadpages login to getUserToken
        $response = $this->leadpagesLogin->getUserToken($this->goodUserName, $this->goodPassword);
        //make sure we veirfy it is a success
        //this also stores the token in the database
        $this->assertEquals('success', $response['responseCode']);
        $this->assertNotEmpty($this->leadpagesLogin->token, 'Leadpages Security Token Not Set');

        //grab the database token and ensure that it matches what was returned, also ensures it was set
        $dbToken = get_option('leadpages_security_token');
        $this->assertEquals($this->leadpagesLogin->token, $dbToken);
    }

    /**
     * User should be logged in so we can ensure that their token is in the database
     *
     * group login_success
     */
    public function test_user_is_logged_in_success()
    {
        $isLoggedIn = $this->leadpagesLogin->isUserLoggedIn();
        $this->assertTrue($isLoggedIn);
    }

}


class Test_LeadpagesLogin_Fail extends WP_UnitTestCase
{

    public $leadpagesLogin;

    public function setUp()
    {
        parent::setUp();
        //setup leadpages login object
        //remind app is setup in parent::setUp();
        $this->leadpagesLogin = $this->app['leadpagesLogin'];
        $this->assertInstanceOf(LeadpagesLogin::class, $this->leadpagesLogin);

    }

    public function tearDown()
    {
        parent::tearDown();
    }


    /**
     * we Should nto get a token back and should get a 401 error
     *
     * @group login_fail
     */

    public function test_get_user_token_fail()
    {
        $response = $this->leadpagesLogin->getUserToken($this->badUserName, $this->badPassword);
        $this->assertNotEquals('success', $response['responseCode']);
        $this->assertEquals('401', $response['responseCode']);
        $this->assertNull($this->leadpagesLogin->token, 'Leadpages Security Token Not Set');
    }

    /**
     * Check to make sure user is not logged in by checking database for a token
     *
     * @group login_fail
     */

    public function test_user_is_logged_in_fail()
    {
        $isLoggedIn = $this->leadpagesLogin->isUserLoggedIn();
        $this->assertFalse($isLoggedIn);
    }

}