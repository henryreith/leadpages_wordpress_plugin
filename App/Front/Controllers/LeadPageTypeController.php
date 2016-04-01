<?php

namespace Leadpages\Front\Controllers;

use Leadpages\Helpers\isLeadPage;
use Leadpages\Helpers\LeadpageType;
use TheLoop\ServiceContainer\ServiceContainerTrait;

class LeadPageTypeController
{

    use ServiceContainerTrait;

    private $ioc;

    public function __construct()
    {
        $this->ioc = $this->getContainer();

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
            if ($post > 0){
                $html = $this->ioc['leadpagesModel']->getHtml($post);
                echo $html; die();
            }
        }
    }


    public function displayWelcomeGate(){
        $welcomeGate = $this->ioc['welcomeGate'];
        $welcomeGate->displayWelcomeGate();
    }

    /**
     * display a normal lead page if page type is a leadpage
     *
     * @param $post
     */

    public function displayNFPage(){
        $nfPage = $this->ioc['nfPage'];
        $nfPage->displaynfPage($this->ioc);
    }
    public function normalPage($post)
    {
        if($post->post_type == 'leadpages_post'){
            $pageID = get_post_meta($post->ID, 'leadpages_page_id');
            $pageID = $pageID[0];
            //TODO check if is split tested and if so dont use cache version
            //non cache version
            $html = $this->ioc['leadpagesModel']->getHtml($post->ID);
            echo $html; die();
        }
    }

    public function initPage()
    {

        $this->displayNFPage();
        $this->displayWelcomeGate();
        $this->isFrontPage();
        $post = get_queried_object();
        if($post) {
            if (!isLeadPage::checkByPost($post)) {
                return;
            }
        $this->normalPage($post);
        }
    }
}