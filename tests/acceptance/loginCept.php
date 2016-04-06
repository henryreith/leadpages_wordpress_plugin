<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Verify that a user can login to leadpages');

$I->amOnPage('/wp-admin');
$I->see('Username');
$I->fillField("#user_login", 'admin');
$I->fillField('#user_pass', 'cd007-01');
$I->click("Log In");
$I->seeInCurrentUrl('wp-admin');
$I->click(['link' => 'Leadpages']);
$I->seeInCurrentUrl('wp-admin/admin.php?page=Leadpages');
$I->fillField("#username", 'brandon.braner@ave81.com');
$I->fillField('#password', 'cd007-01');
$I->click('Sign In');
$I->seeInCurrentUrl('edit.php?post_type=leadpages_post');