<?php


class LeadpagesPostTypeTestCest
{
    protected $testdata;

    public function _before(AcceptanceTester $I)
    {
        $this->testdata = require(dirname(dirname(__FILE__))).'/_data/testdata.php';
    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @param \AcceptanceTester $I
     */
    public function CheckPageTypesExist(AcceptanceTester $I)
    {
        $I->wantTo('Make sure that I see Leadpage Types in the types dropdown');
        $I->login($this->testdata);
        $I->goToLeadpagesPostType();
        //wait for element to load being its hidden via JS
        $I->waitForElement('form select[name=leadpages-post-type]', 10);
        //need to wait 3 seconds so we can manipulate the field
        $I->wait(5);
        $I->selectOption('form select[name=leadpages-post-type]', 'Normal Page');
        $I->selectOption('form select[name=leadpages-post-type]', 'Home Page');
        $I->selectOption('form select[name=leadpages-post-type]', 'Welcome Gate™');
        $I->selectOption('form select[name=leadpages-post-type]', '404 Page');

        $I->wantTo('Make sure that I see Leadpage pages dropdown');
        $I->selectOption('form select[name=leadpages_my_selected_page]', 'E2E Test Page Do Not Remove');

    }



}
