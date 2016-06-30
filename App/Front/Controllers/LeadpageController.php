<?php

namespace LeadpagesWP\Front\Controllers;

use Leadpages\Pages\LeadpagesPages;
use LeadpagesWP\Helpers\LeadpageType;
use LeadpagesWP\Helpers\PasswordProtected;
use LeadpagesWP\models\LeadPagesPostTypeModel;

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
    /**
     * @var \LeadpagesWP\Helpers\PasswordProtected
     */
    private $passwordChecker;

    public function __construct(NotFoundController $notfound, WelcomeGateController $welcomeGate ,LeadPagesPostTypeModel $leadpagesModel, LeadpagesPages $pagesApi, PasswordProtected $passwordChecker)
    {

        $this->leadpagesModel = $leadpagesModel;
        $this->notfound = $notfound;
        $this->welcomeGate = $welcomeGate;
        $this->pagesApi = $pagesApi;
        $this->passwordChecker = $passwordChecker;
    }

    /**
     * check to see if current page is front page and if so see if a front
     * leadpage exists to display it
     *
     * @param $posts
     *
     * @return
     */
    public function isFrontPage($posts)
    {
        if (is_home() || is_front_page()) {
            //see if a front page exists
            $post = LeadpageType::get_front_lead_page();

            //if $post is > 0 that means one exists and we need to display it
            if ($post > 0) {
                $pageId = $this->leadpagesModel->getLeadpagePageId($post);

                //check for cache
                $getCache = get_post_meta($pageId, 'cache_page', true);
                if($getCache == true){
                    $html = $this->postTypeModel->getCacheForPage($pageId);
                    if(empty($html)){
                        $apiResponse = $this->pagesApi->downloadPageHtml($pageId);
                        $html = $apiResponse['response'];
                        $this->leadpagesModel->setCacheForPage($pageId);
                    }
                }else {
                    //no cache download html
                    $apiResponse = $this->pagesApi->downloadPageHtml($pageId);
                    $html = $apiResponse['response'];
                }
                echo $html;
                die();
            }
        }
        return $posts;
    }

    /**
     *Display WelcomeGate Page
     */
    public function displayWelcomeGate($posts)
    {
        return $this->welcomeGate->displayWelcomeGate($posts);
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
     * Echos a normal Leadpage type html if the post type is leadpages_post
     * @param $post
     */
    public function normalPage()
    {
        //get page uri
        $requestedPage = $this->parse_request();
        if ( false == $requestedPage ) {
            return false;
        }
        //get post from database including meta data
        $post = LeadPagesPostTypeModel::get_all_posts($requestedPage[0]);

        if($post == false) return false;

        //ensure we have the leadpages page id
        if(isset($post['leadpages_page_id'])){
            $pageId = $post['leadpages_page_id'];
        }elseif(isset($post['leadpages_my_selected_page'])){
            $pageId = $this->leadpagesModel->getPageByXORId($post['post_id'], $post['leadpages_my_selected_page']);
        }else{
            return false;
        }
        //return false if no page id is found
//        if(empty($pageId)) return false;
//
//        if (!empty($posts) || $this->passwordChecker->getPostPassword($post['post_id'])) {
//            $passwordEntered = $this->passwordChecker->checkWPPasswordHash($post['post_id'], COOKIEHASH);
//            if ($passwordEntered) {
//                $result = $this->leadpageController->normalPage();
//                if ($result == false) {
//                    return $posts;
//                }
//            } else {
//
//                return;
//            }
//        }

        //check cache
        $getCache = get_post_meta($post['post_id'], 'cache_page', true);
        if($getCache == true){
            $html = $this->leadpagesModel->getCacheForPage($pageId);
            //failsafe incase the cache is not set for some reason
            //get html and set cache
            if(empty($html)){
                $apiResponse = $this->pagesApi->downloadPageHtml($pageId);
                $html = $apiResponse['response'];
                $this->leadpagesModel->setCacheForPage($pageId);
            }
        }else {
            $apiResponse = $this->pagesApi->downloadPageHtml($pageId);
            $html = $apiResponse['response'];
            print_r($html);die();
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
        foreach($tokens as $index => $token){
            //decode url enteities such as %20 for space
            $tokens[$index] = urldecode($token);
        }
        return $tokens;
    }

}