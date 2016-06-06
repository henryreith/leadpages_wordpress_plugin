<?php

namespace Leadpages\Bootstrap;

use Leadpages\Admin\Factories\SettingsPage;
use Leadpages\ServiceProviders\LeadpagesLogin;
use Leadpages\Admin\SettingsPages\LeadpagesLoginPage;

class AdminBootstrap
{

    /**
     * @var \Leadpages\ServiceProviders\LeadpagesLogin
     */
    private $login;

    public function __construct(LeadpagesLogin $login)
    {
        $this->login = $login;
        $this->setupLogin();
    }

    public function setupLogin()
    {
        if (!$this->login->isUserLoggedIn()) {
            //create login form page if user is not logged in
            SettingsPage::create(LeadpagesLoginPage::getName());
            //register hook to listen for admin post of login form
            $this->login->loginHook();
        }
    }

    public function setupLeadpages()
    {

    }

    public function setupLeadboxes()
    {

    }
}