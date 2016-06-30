<?php


class LoginCest
{

    protected $testData;
    protected $badData;

    public function _before(AcceptanceTester $I)
    {
        $this->testData = require(dirname(dirname(__FILE__))).'/_data/testdata.php';
        $this->badData = [
          'leadpagesUsername' => 'badusername@aol.com',
          'leadpagesPassword' => 'aolrocks',
          'adminUsername'     => $this->testData['adminUsername'],
          'adminPassword'     => $this->testData['adminPassword'],
        ];
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    /**
     * @param \AcceptanceTester $I
     * @group login_test
     */
    public function test_user_is_able_to_login(AcceptanceTester $I)
    {

        $I->login($this->testData);
    }


    /**
     * @param \AcceptanceTester $I
     * @group login_test
     */
    public function test_user_is_unable_to_login(AcceptanceTester $I)
    {
        $I->amOnPage('/wp-admin');
        $I->see('Username');
        $I->fillField("#user_login", $this->badData['adminUsername']);
        $I->fillField('#user_pass', $this->badData['adminPassword']);
        $I->click("Log In");
        $I->seeInCurrentUrl('wp-admin');
        $I->see('Welcome to WordPress!');
        $I->click('Leadpages');

        $I->seeInCurrentUrl('wp-admin/admin.php?page=Leadpages');
        $I->fillField("username", $this->badData['leadpagesUsername']);
        $I->fillField('password', $this->badData['leadpagesPassword']);
        $I->click('Sign In');
        $I->seeInCurrentUrl('/wordpress/wp-admin/admin.php?page=Leadpages&code=401');
        $I->canSee('Login Failed Error Code: 401');
    }
}
