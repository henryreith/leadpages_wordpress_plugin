<?php


namespace LeadpagesWP\Front\Controllers;

use LeadpagesWP\Helpers\LeadpageType;

class WelcomeGateController
{

    protected $welcomeGateId;
    protected $welcomeGateUrl;

    protected function welcomeGateExists()
    {
        $this->welcomeGateId = LeadpageType::get_wg_lead_page();
        if (!$this->welcomeGateId) {
            return false;
        }

        return true;
    }

    protected function setWelcomeGateCookie()
    {
        setcookie('leadpages-welcome-gate-displayed', '1', time() + 60 * 60 * 24 * 365);
    }

    protected function checkWelcomeGateCookie()
    {
        if (isset($_COOKIE['leadpages-welcome-gate-displayed'])) {
            return true;
        }

        return false;
    }

    protected function getWelcomeGateUrl(){
        $this->welcomeGateUrl = get_post_meta($this->welcomeGateId, 'leadpages_slug', true);
    }

    protected function welcomeGateHttpRedirect(){
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
        header( 'Location: ' . $this->welcomeGateUrl, true, 307 );
        die();
    }

    public function displayWelcomeGate()
    {

        $cookieIsSet = $this->checkWelcomeGateCookie();
        if($cookieIsSet){
            return;
        }
        if($this->welcomeGateExists() && !$this->checkWelcomeGateCookie()){

            $this->getWelcomeGateUrl();
            $this->setWelcomeGateCookie();
            $this->welcomeGateHttpRedirect();
        }

    }

}