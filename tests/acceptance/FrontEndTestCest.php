<?php


class FrontEndTestCest
{
    public $testdata;

    public function _before(AcceptanceTester $I)
    {
        $this->testdata = require(dirname(dirname(__FILE__))).'/_data/testdata.php';

    }

    public function _after(AcceptanceTester $I)
    {
    }

    /**
     * @group verifyFrontEnd
     *
     * @param \AcceptanceTester $I
     */
    public function Check_Normal_Page(AcceptanceTester $I){
        $I->wantTo('Create a Normal Leadpage in the admin then go to the url for that Leadpage and see that it displays properly');
        $I->login($this->testdata);
        $I->goToLeadpagesPostType();
        //wait for element to load being its hidden via JS
        $I->waitForElement('#leadpages_slug_input', 10);
        //need to wait 5 seconds so we can manipulate the field
        $I->wait(5);
        $I->fillField('#leadpages_slug_input', 'normal_page');
        $I->selectOption('form select[name=leadpages-post-type]', 'Normal Page');
        $I->selectOption('form select[name=leadpages_my_selected_page]', 'E2E Test Page Do Not Remove');
        $I->selectOption('form input[name=cache_this]', 'true');
        $I->click('#publish');
        //set welcomegate cookie so page is actually displayed
        $I->setCookie('leadpages-welcome-gate-displayed', '1');
        $I->amOnPage('normal_page');
        $I->see('End To End Test Page Working');
    }

    /**
     * @group verifyFrontEnd
     *
     * @param \AcceptanceTester $I
     */
    public function Check_WelcomeGate(AcceptanceTester $I){
        $I->wantTo('Create a Welcomegate Leadpage in the admin then go to the url for that Leadpage and see that it displays properly');
        $I->resetCookie('leadpages-welcome-gate-displayed');
        $I->login($this->testdata);
        $I->goToLeadpagesPostType();
        //wait for element to load being its hidden via JS
        $I->waitForElement('#leadpages_slug_input', 10);
        //need to wait 5 seconds so we can manipulate the field
        $I->wait(5);
        $I->fillField('#leadpages_slug_input', 'welcome_gate');
        $I->selectOption('form select[name=leadpages-post-type]', 'Welcome Gate');
        $I->selectOption('form select[name=leadpages_my_selected_page]', 'E2E Test Page Do Not Remove');
        $I->selectOption('form input[name=cache_this]', 'true');

        $I->click('#publish');
        //got to the page
        $I->resetCookie('leadpages-welcome-gate-displayed');
        //Go To Homepage because welcome gate page should then fire
        $I->amOnPage('/');
        //check for text
        $I->see('End To End Test Page Working');
        //check for cookie to be set
        //$I->seeCookie('leadpages-welcome-gate-displayed');
    }


    /**
     * @group verifyFrontEnd
     *
     * @param \AcceptanceTester $I
     */
    public function Check_Homepage(AcceptanceTester $I){
        $I->wantTo('Create a Homepage Leadpage in the admin then go to the url for that Leadpage and see that it displays properly');
        $I->login($this->testdata);
        $I->goToLeadpagesPostType();
        //wait for element to load being its hidden via JS
        $I->waitForElement('#leadpages_slug_input', 10);
        //need to wait 5 seconds so we can manipulate the field
        $I->wait(5);
        $I->fillField('#leadpages_slug_input', 'homepage');
        $I->selectOption('form select[name=leadpages-post-type]', 'Home Page');
        $I->selectOption('form select[name=leadpages_my_selected_page]', 'E2E Test Page Do Not Remove');
        $I->selectOption('form input[name=cache_this]', 'true');

        $I->click('#publish');
        //set welcomegate cookie so page is actually displayed
        $I->setCookie('leadpages-welcome-gate-displayed', '1');
        $I->amOnPage('/');
        $I->see('End To End Test Page Working');
    }

    /**
     * @group verifyFrontEnd
     *
     * @param \AcceptanceTester $I
     */
    public function Check_404(AcceptanceTester $I){
        $I->wantTo('Create a 404 Leadpage in the admin then go to the url for that Leadpage and see that it displays properly');
        $I->login($this->testdata);
        $I->goToLeadpagesPostType();
        //wait for element to load being its hidden via JS
        $I->waitForElement('#leadpages_slug_input', 10);
        //need to wait 5 seconds so we can manipulate the field
        $I->wait(5);
        $I->fillField('#leadpages_slug_input', '404 Page');
        $I->selectOption('form select[name=leadpages-post-type]', '404 Page');
        $I->selectOption('form select[name=leadpages_my_selected_page]', 'E2E Test Page Do Not Remove');
        $I->selectOption('form input[name=cache_this]', 'true');

        $I->click('#publish');
        //set welcomegate cookie so page is actually displayed
        $I->setCookie('leadpages-welcome-gate-displayed', '1');
        $I->amOnPage('thispagedoesnotexist');
        $I->see('End To End Test Page Working');
    }
}
