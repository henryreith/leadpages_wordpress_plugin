<?php

use LeadpagesWP\ServiceProviders\WordPressLeadpagesAuth;

class Test_LeadpagesLogin_Success extends WP_UnitTestCase
{

    public $leadpagesLogin;
    public $stub;

    public function setUp()
    {
        parent::setUp();
        //setup leadpages login object
        $this->stub = $this->getMockBuilder(WordPressLeadpagesAuth::class)
            ->setConstructorArgs([new GuzzleHttp\Client()])
            ->setMethods(['getToken'])
            ->getMock();

        $this->stub->token = getenv('testToken');

        $this->stub->expects($this->any())
                   ->method('getToken')
                   ->will($this->returnValue($this->stub->token));


        //set to true to simulate getting back a true response from api call


    }

    public function tearDown()
    {
        parent::tearDown();
        //unset token after every test so it can be retested
        unset($this->leadpagesLogin->token);
    }


    /**
     * checks to see if $this->token is empty when it is fetched from the database
     *
     * @group login_success
     */
    public function test_check_if_token_is_empty()
    {
        $result = $this->stub->checkIfTokenIsEmpty();
        $error = $result['error'];
        $code = $result['code'];

        $this->assertFalse($error);
        $this->assertEquals('200', $code);
    }

    /**
     * User should be logged in so we can ensure that their token is in the database
     *
     * @group login_success
     */
    public function test_user_is_logged_in_success()
    {
        //stub out $this->getToken above to return a token preset in env.
        //makes actually call
        $result = $this->stub->isLoggedIn();
        $this->assertTrue($result);
    }

    /**
     * User should be able to login
     *
     * @group login_success
     */

    public function test_login()
    {
        //preset post variables
        $_POST['username'] = sanitize_text_field(getenv('username'));
        $_POST['password'] = sanitize_text_field(getenv('password'));

        $response = $this->stub->login();

        $this->assertEquals('success', $response);

    }

}


class Test_LeadpagesLogin_Fail extends WP_UnitTestCase
{

    public $leadpagesLogin;
    public $stub;

    public function setUp()
    {
        parent::setUp();
        //setup leadpages login object
        $this->stub = $this->getMockBuilder(WordPressLeadpagesAuth::class)
                           ->setConstructorArgs([new GuzzleHttp\Client()])
                           ->setMethods(['getToken'])
                           ->getMock();

        $this->stub->expects($this->any())
                   ->method('getToken')
                   ->will($this->returnValue($this->stub->token));


        //set to true to simulate getting back a true response from api call


    }

    public function tearDown()
    {
        parent::tearDown();
        //unset token after every test so it can be retested
        unset($this->leadpagesLogin->token);
    }


    /**
     * checks to see if $this->token is empty when it is fetched from the database
     *
     * @group login-fail
     */
    public function test_check_if_token_is_empty_fail()
    {
        $result = $this->stub->checkIfTokenIsEmpty();
        $error = $result['error'];
        $code = $result['code'];

        $this->assertTrue($error);
        $this->assertEquals('500', $code);
    }

    /**
     * User should be logged in so we can ensure that their token is in the database
     *
     * @group login-fail
     */
    public function test_user_is_logged_in_fail()
    {
        //stub out $this->getToken above to return a token preset in env.
        //makes actually call
        $result = $this->stub->isLoggedIn();
        $this->assertFalse($result);
    }

    /**
     * @group login-fail
     */
    public function test_login_fail()
    {
        //preset post variables
        $_POST['username'] = 'unittest@wordpressplugintest.com';
        $_POST['password'] = 'example';

        $response = $this->stub->login();
        $response = json_decode($response, true);

        $this->assertEquals('401', $response['code']);

    }

}