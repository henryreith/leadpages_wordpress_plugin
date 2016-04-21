<?php


class LeadpagesPostTypeTestCest
{
    protected $testdata="";

    public function _before(AcceptanceTester $I)
    {
        $this->testdata = require(dirname(dirname(__FILE__))).'/_data/testdata.php';

    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function CheckPageTypesExist(AcceptanceTester $I)
    {
        $I->wantTo('Make sure that I see Leadpage Types in the types dropdown');
        $I->login($this->testdata['leadpagesUsername'], $this->testdata['leadpagesPassword']);
        $I->goToLeadpagesPostType();
        $I->selectOption('form select[name=leadpages-post-type]', 'Normal Page');
        $I->selectOption('form select[name=leadpages-post-type]', 'Home Page');
        $I->selectOption('form select[name=leadpages-post-type]', 'Welcome Gate™');
        $I->selectOption('form select[name=leadpages-post-type]', '404 Page');

        $I->wantTo('Make sure that I see Leadpage pages dropdown');
        $I->selectOption('form select[name=leadpages_my_selected_page]', 'E2E Test Page Do Not Remove');

    }

    protected function CheckLeadpagesInDropdown(AcceptanceTester $I)
    {

        $I->login($this->testdata['leadpagesUsername'], $this->testdata['leadpagesPassword']);
        $I->goToLeadpagesPostType();
    }

    /**
     * @group verifyFrontEnd
     */
    public function CheckLeadpageVisibleOnFrontEnd(AcceptanceTester $I){
        $I->wantTo('Create a Leadpage in the admin then go to the url for that Leadpage and see that it displays properly');
        $I->login($this->testdata['leadpagesUsername'], $this->testdata['leadpagesPassword']);
        $I->goToLeadpagesPostType();
        $I->fillField('post_title', 'E2E Test Page');
        $I->selectOption('form select[name=leadpages-post-type]', 'Normal Page');
        $I->selectOption('form select[name=leadpages_my_selected_page]', 'E2E Test Page Do Not Remove');
        $I->click('#publish');
        $I->click('#edit-slug-box a');
        $I->see('This is my test page and its working');
    }

}
