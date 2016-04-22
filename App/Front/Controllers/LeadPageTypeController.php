<?php

namespace Leadpages\Front\Controllers;

use Leadpages\Helpers\IsLeadPage;
use Leadpages\Helpers\LeadpageType;
use TheLoop\ServiceContainer\ServiceContainerTrait;

class LeadPageTypeController
{

    use ServiceContainerTrait;

    private $ioc;

    public function __construct()
    {
        $this->ioc             = $this->getContainer();
        $this->passwordChecker = $this->ioc['passwordProtected'];

    }

    /**
     * check to see if current page is front page and if so see if a front
     * leadpage exists to display it
     *
     * @param $post_id
     */
    public function isFrontPage()
    {
        if (is_home() || is_front_page()) {
            //see if a front page exists
            $post = LeadpageType::get_front_lead_page();

            //if $post is > 0 that means one exists and we need to display it
            if ($post > 0) {
                $html = $this->ioc['leadpagesModel']->getHtml($post);
                echo $html;
                die();
            }
        }
    }


    public function displayWelcomeGate()
    {
        $welcomeGate = $this->ioc['welcomeGate'];
        $welcomeGate->displayWelcomeGate();
    }

    /**
     * display a normal lead page if page type is a leadpage
     *
     * @param $post
     */

    public function displayNFPage()
    {
        $nfPage = $this->ioc['nfPage'];
        $nfPage->displaynfPage($this->ioc);
    }

    public function normalPage($post)
    {
        if ($post->post_type == 'leadpages_post') {

            $html = $this->ioc['leadpagesModel']->getHtml($post->ID);
            echo $html;
            die();
        }
    }

    public function initPage()
    {
        global $config;

        //check all special pagetypes first, then go to a normal leadpage if not matched
        //if any resource is a 404 it will try to load this as well, causing page load latency
        $this->displayNFPage();
        $this->displayWelcomeGate();
        $this->isFrontPage();

        //permalinks appear unreliable, have to get the page id by the url
        $post = IsLeadPage::isLeadPageUrlQuery();
        if(!$post){
            return;
        }

        if ($this->passwordChecker->getPostPassword($post)) {
            $passwordEntered = $this->passwordChecker->checkWPPasswordHash($post, COOKIEHASH);
            if ($passwordEntered) {
                $this->normalPage($post);
            } else {
                return;
            }
        }

        $this->normalPage($post);

    }

}