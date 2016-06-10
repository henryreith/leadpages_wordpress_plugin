<?php

namespace LeadpagesWP\Bootstrap;

use LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType;
use LeadpagesWP\Admin\Factories\CustomPostType;
use LeadpagesWP\Admin\Factories\SettingsPage;
use LeadpagesWP\Admin\SettingsPages\LeadpagesLoginPage;
use LeadpagesWP\ServiceProviders\WordPressLeadpagesAuth;

class AdminBootstrap
{

    /**
     * @var \Leadpages\ServiceProviders\LeadpagesLogin
     */
    private $login;
    public $isLoggedIn;

    public function __construct(WordPressLeadpagesAuth $login)
    {
        $this->login = $login;
        $this->setupLogin();
        $this->setupLeadpages();
    }

    public function setupLogin()
    {
        if(!$this->login->isLoggedIn())
        {
            //create login form page if user is not logged in
            SettingsPage::create(LeadpagesLoginPage::getName());
            //register hook to listen for admin post of login form
            $this->login->loginHook();
        }else {
            $this->login->setLoggedInCookie();
            $this->isLoggedIn = true;
        }
    }

    public function setupLeadpages()
    {
        //dont execute if not logged in
        if(!$this->isLoggedIn) return;

        CustomPostType::create(LeadpagesPostType::getName());

    }

    public function setupLeadboxes()
    {
        //dont execute if not logged in
        if(!$this->isLoggedIn) return;

    }
}