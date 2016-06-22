<?php

namespace LeadpagesWP\Bootstrap;

use Leadpages\Pages\LeadpagesPages;
use LeadpagesWP\models\LeadPagesPostTypeModel;
use LeadpagesWP\Admin\Factories\CustomPostType;
use LeadpagesWP\Front\Controllers\LeadpageController;
use LeadpagesWP\ServiceProviders\WordPressLeadpagesAuth;
use LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType;

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

    public function __construct(WordPressLeadpagesAuth $login, LeadpageController $leadpageController, LeadpagesPages $pagesApi)
    {
        $this->login = $login;
        $this->setupLeadpages();
        $this->pagesApi = $pagesApi;
        $this->leadpageController = $leadpageController;
        add_filter('post_type_link', array( &$this, 'leadpages_permalink' ), 99, 2);

        $this->leadpageController->displayWelcomeGate();
        add_action('template_redirect', array($this->leadpageController, 'displayNFPage'));
        add_action('the_posts', array($this->leadpageController, 'isFrontPage'));
        add_action('the_posts', array($this, 'displayLeadpage'));
    }

    public function setupLeadpages()
    {
        //dont execute if not logged in
        if (!$this->login->isLoggedIn()) {
            return;
        }
        CustomPostType::create(LeadpagesPostType::getName());

    }

    public function displayLeadpage($posts)
    {
        if (!empty($posts)) {
            if($posts[0]->post_type != 'leadpages_post') return $posts;
        }

        $result = $this->leadpageController->normalPage();
        if ($result == false) {
            return $posts;
        }
    }

    public function leadpages_permalink( $url, $post ) {
        if ( 'leadpages_post' == get_post_type( $post ) ) {
            $path = esc_html( get_post_meta( $post->ID, 'leadpages_slug', true ) );
            if ( $path != '' ) {
                return site_url() . '/' . $path;
            } else {
                return '';
            }
        }

        return $url;
    }

}