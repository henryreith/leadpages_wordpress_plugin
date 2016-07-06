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
        header( "Expires: Tue, 03 Jul 2001 06:00:00 GMT" );
        header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
        header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
        header( "Cache-Control: post-check=0, pre-check=0", false );
        header( "Pragma: no-cache" );
        header( 'X-Random-Header: ' . ( rand() + time() ) );
        header( 'Location: ' . $this->welcomeGateUrl, true, 307 );
        die();
    }

    public function displayWelcomeGate($posts)
    {
        if($this->checkWelcomeGateCookie()){
            return $posts;
        }
        if($this->welcomeGateExists() && !$this->checkWelcomeGateCookie()){

            $this->getWelcomeGateUrl();
            $this->setWelcomeGateCookie();
            $this->welcomeGateHttpRedirect();
        }

        return $posts;

    }

}