<?php

namespace Leadpages\Bootstrap;

use Leadpages\Front\Controllers\LeadPageTypeController;
use Leadpages\Admin\Factories\CustomPostType;
use Leadpages\Admin\Providers\LeadpagesPagesApi;
use TheLoop\ServiceContainer\ServiceContainerTrait;
use Leadpages\Admin\CustomPostTypes\LeadpagesPostType;

class FrontBootstrap
{
    use ServiceContainerTrait;

    /**
     * @var \Leadpages\admin\Providers\LeadpagesPagesApi
     */
    private $pagesApi;
    public $postId;
    public $postType;
    public $leadpagesPostType;

    public function __construct(LeadpagesPagesApi $pageApi, LeadpagesPostType $leadpagesPostType) {

        $this->pagesApi   = $pageApi;
        $this->postId = get_the_ID();
        $this->postType = get_post_type($this->postId);
        $this->leadpagesPostType = $leadpagesPostType;
        $this->initFront();

    }

    public function initFront()
    {
        CustomPostType::create(LeadpagesPostType::getName());
        add_action( 'pre_get_posts', array($this->leadpagesPostType, 'parse_request_trick' ));
        add_action( 'wp_enqueue_scripts', array($this, 'loadJS') );
        $controller = new LeadPageTypeController();
        add_action('wp', array($controller, 'initPage'));
    }


    public function loadJS()
    {
        global $config;
        wp_enqueue_script('LeadpagesPostPassword',
          $config['admin_assets'] . '/js/LeadpagesPostPassword.js',
          array('jquery'));
        wp_localize_script('LeadpagesPostPassword', 'ajax_object', array(
          'ajax_url' => admin_url('admin-ajax.php'),
          'id'       => get_the_ID()
        ));
    }


}