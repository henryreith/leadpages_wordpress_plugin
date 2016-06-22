<?php

namespace LeadpagesWP\Front\Controllers;

use Leadpages\Pages\LeadpagesPages;
use LeadpagesWP\Helpers\LeadpageType;
use LeadpagesWP\models\LeadPagesPostTypeModel;
use LeadpagesWP\Front\Controllers\WelcomeGateController;
use LeadpagesWP\Front\Controllers\NotFoundController;

class LeadpageController
{


    /**
     * @var \LeadpagesWP\models\LeadPagesPostTypeModel
     */
    private $leadpagesModel;
    /**
     * @var \LeadpagesWP\Front\Controllers\NotFoundController
     */
    private $notfound;
    /**
     * @var \LeadpagesWP\Front\Controllers\WelcomeGateController
     */
    private $welcomeGate;
    /**
     * @var \Leadpages\Pages\LeadpagesPages
     */
    private $pagesApi;

    public function __construct(NotFoundController $notfound, WelcomeGateController $welcomeGate ,LeadPagesPostTypeModel $leadpagesModel, LeadpagesPages $pagesApi)
    {

        $this->leadpagesModel = $leadpagesModel;
        $this->notfound = $notfound;
        $this->welcomeGate = $welcomeGate;
        $this->pagesApi = $pagesApi;
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
                $pageId = $this->leadpagesModel->getLeadpagePageId($post);

                $html = $this->pagesApi->downloadPageHtml($pageId);
                echo $html;
                die();
            }
        }
    }

    /**
     *Display WelcomeGate Page
     */
    public function displayWelcomeGate()
    {
        $this->welcomeGate->displayWelcomeGate();
    }

    /**
     * display a normal lead page if page type is a leadpage
     *
     * @param $post
     */

    public function displayNFPage()
    {

        $this->notfound->displaynfPage();
    }

    /**
     * Return a normal Leadpage type if the post type is leadpages_post
     * @param $post
     */
    public function normalPage()
    {
        global $wpdb;

        //get page uri
        $requestedPage = $this->parse_request();
        if ( false == $requestedPage ) {
            return false;
        }
        //get post from database including meta data
        $post = LeadPagesPostTypeModel::get_all_posts($requestedPage[0]);

        //return posts if this isn't a leadpage
        //check leadpages_page_id(new pages from new plugin) and xhor id from old plugin
        if($post == false || !isset($post['leadpages_page_id']) || !isset($post['leadpages_my_selected_page'])) return false;

        $pageId = '';
        if(isset($post['leadpages_page_id'])){
            $pageId = $post['leadpages_page_id'];
        }elseif(isset($post['leadpages_my_selected_page'])){
            $pageId = $this->leadpagesModel->getPageByXORId($post['leadpages_my_selected_page']);
        }

        if(empty($pageId)) return false;

        //check cache
        $getCache = get_post_meta($post['post_id'], 'cache_page', true);
        if($getCache == true){
            $html = $this->leadpagesModel->getCacheForPage($pageId);
        }else {
            $html = $this->pagesApi->downloadPageHtml($post['leadpages_page_id']);
        }
        if(ob_get_length() > 0){
            ob_clean();
        }
        ob_start();//start output buffer
        status_header( '200' );
        print $html;
        ob_end_flush();
        die();
    }

    function parse_request() {
        // get current url
        $current = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        // calculate the path
        $part = substr( $current, strlen( site_url() ) );
        if ( $part[0] == '/' ) {
            $part = substr( $part, 1 );
        }
        // strip parameters
        $real   = explode( '?', $part );
        $tokens = explode( '/', $real[0] );
        return $tokens;
    }

}