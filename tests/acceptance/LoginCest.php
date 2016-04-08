<?php


class LoginCest
{
    protected $testdata="";

    public function _before(AcceptanceTester $I)
    {
        $this->testdata = require(dirname(dirname(__FILE__))).'/_data/testdata.php';

    }

    public function _after(AcceptanceTester $I)
    {
    }

    protected function login(AcceptanceTester $I){

        $I->wantTo('Verify that a user can login to leadpages');

        $I->amOnPage('/wp-admin');
        $I->see('Username');
        $I->fillField("#user_login", 'admin');
        $I->fillField('#user_pass', 'cd007-01');
        $I->click("Log In");
        $I->seeInCurrentUrl('wp-admin');
        $I->see('Welcome to WordPress!');
        $I->click('Leadpages');

        $I->seeInCurrentUrl('wp-admin/admin.php?page=Leadpages');
        $I->fillField("username", $this->testdata['leadpagesUsername']);
        $I->fillField('password', $this->testdata['leadpagesPassword']);
        $I->click('Sign In');
        $I->seeInCurrentUrl('edit.php?post_type=leadpages_post');
        $I->seeInDatabase("wp_options", array("option_name" => "leadpages_security_token"));
    }

    /**
     * @before login
     */
    public function CheckPageTypesExist(AcceptanceTester $I)
    {
        $I->wantTo('Make sure that I see Leadpage Types in the types dropdown');
        $I->amOnPage('wordpress-mu/wp-admin/edit.php?post_type=leadpages_post');
        $I->see("LeadPages", "h1");
        $I->click(".page-title-action");
        $I->selectOption('form select[name=leadpages-post-type]', 'Normal Page');
        $I->selectOption('form select[name=leadpages-post-type]', 'Home Page');
        $I->selectOption('form select[name=leadpages-post-type]', 'Welcome Gate™');
        $I->selectOption('form select[name=leadpages-post-type]', '404 Page');
    }

    /**
     * @before login
     */
    public function CheckPagesExistInDropDown(AcceptanceTester $I){
        $I->wantTo('Make sure that I see Leadpage in dropdown');
        $I->amOnPage('wordpress-mu/wp-admin/edit.php?post_type=leadpages_post');
        $I->see("LeadPages", "h1");
        $I->click(".page-title-action");
        $I->selectOption('form select[name=leadpages-post-type]', '~[^None](a-z)*~i');

    }
}
