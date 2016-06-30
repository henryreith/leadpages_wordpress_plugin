<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    public function login($testData){

        $I = $this;
        $I->amOnPage('/wp-admin');
        $I->see('Username');
        $I->fillField("#user_login", $testData['adminUsername']);
        $I->fillField('#user_pass', $testData['adminPassword']);
        $I->click("Log In");
        $I->seeInCurrentUrl('wp-admin');
        $I->see('Welcome to WordPress!');
        $I->click('Leadpages');

        $I->seeInCurrentUrl('wp-admin/admin.php?page=Leadpages');
        $I->fillField("username", $testData['leadpagesUsername']);
        $I->fillField('password', $testData['leadpagesPassword']);
        $I->click('Sign In');
        $I->seeInCurrentUrl('edit.php?post_type=leadpages_post');
        $I->seeInDatabase("wp_options", array("option_name" => "leadpages_security_token"));
    }

    public function goToLeadpagesPostType(){
        $I=$this;
        $I->amOnPage('wp-admin/edit.php?post_type=leadpages_post');
        $I->see("LeadPages", "h1");
        $I->click(".page-title-action");
    }


}
