<?php

namespace LeadpagesWP\Bootstrap;


use Leadpages\Pages\LeadpagesPages;
use LeadpagesWP\Admin\Factories\CustomPostType;
use LeadpagesWP\Front\ShortCodes\LeadboxShortCodes;
use LeadpagesWP\Front\Controllers\LeadboxController;
use LeadpagesWP\Front\Controllers\LeadpageController;
use LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType;
use LeadpagesWP\ServiceProviders\WordPressLeadpagesAuth;

class FrontBootstrap
{

    /**
     * @var \LeadpagesWP\Bootstrap\WordPressLeadpagesAuth
     */
    private $login;
    /**
     * @var \LeadpagesWP\Front\Controllers\LeadpageController
     */
    private $leadpageController;
    /**
     * @var \Leadpages\Pages\LeadpagesPages
     */
    private $pagesApi;
    /**
     * @var \LeadpagesWP\Front\Controllers\LeadboxController
     */
    private $leadboxController;
    /**
     * @var \LeadpagesWP\Front\ShortCodes\LeadboxShortCodes
     */
    private $leadboxShortCodes;

    /**
     * FrontBootstrap constructor.
     *
     * @param \LeadpagesWP\ServiceProviders\WordPressLeadpagesAuth $login
     * @param \LeadpagesWP\Front\Controllers\LeadpageController $leadpageController
     * @param \Leadpages\Pages\LeadpagesPages $pagesApi
     * @param \LeadpagesWP\Front\Controllers\LeadboxController $leadboxController
     * @param \LeadpagesWP\Front\ShortCodes\LeadboxShortCodes $leadboxShortCodes
     */
    public function __construct(
      WordPressLeadpagesAuth $login,
      LeadpageController $leadpageController,
      LeadpagesPages $pagesApi,
      LeadboxController $leadboxController,
      LeadboxShortCodes $leadboxShortCodes
    )
    {
        $this->login              = $login;
        $this->pagesApi           = $pagesApi;
        $this->leadpageController = $leadpageController;
        $this->leadboxController  = $leadboxController;
        $this->leadboxShortCodes = $leadboxShortCodes;


        if(!$this->login->isLoggedIn()){
            return;
        }

        $this->setupLeadpages();
        add_filter('post_type_link', array($this, 'leadpages_permalink'), 1, 2);
        add_filter('the_posts', array($this, 'displayLeadpage'), 1);
        add_filter('the_posts', array($this->leadpageController, 'displayWelcomeGate'));
        add_action('template_redirect', array($this->leadpageController, 'displayNFPage'));
        add_action('wp', array($this->leadpageController, 'isFrontPage'));
        add_action('wp', array($this, 'displayLeadboxes'));
        $this->leadboxShortCodes->addLeadboxesShortCode();

    }

    /**
     * Create leadpages custom post type from factory
     */
    public function setupLeadpages()
    {
        CustomPostType::create(LeadpagesPostType::getName());
    }

    /**
     * display a leadpage if its not a homepage or a 404 page
     * @param $posts
     */
    public function displayLeadpage($posts)
    {
        if(is_front_page() || is_home() || is_404()) return;
        
        $result = $this->leadpageController->normalPage();
        if ($result == false) {
            return $posts;
        }
    }

    /**
     * display leadboxes on normal and 404 pages
     */
    public function displayLeadboxes()
    {
        if(!is_404()){
            add_action('get_footer', array($this->leadboxController, 'initLeadboxes'));
        }else{
            add_action('get_footer', array($this->leadboxController, 'initLeadboxes404'));
        }
    }


    /**
     * create url structure for leadpages post type so it does not include leadpages_post in the url
     *
     * @param $url
     * @param $post
     *
     * @return string
     */
    public function leadpages_permalink($url, $post)
    {
        if ('leadpages_post' == get_post_type($post)) {
            $path = esc_html(get_post_meta($post->ID, 'leadpages_slug', true));
            if ($path != '') {
                return site_url() . '/' . $path;
            } else {
                return '';
            }
        }

        return $url;
    }

}