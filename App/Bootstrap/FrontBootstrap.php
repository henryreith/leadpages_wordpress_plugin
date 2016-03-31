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
        CustomPostType::create(LeadpagesPostType::class);
        add_action( 'pre_get_posts', array($this->leadpagesPostType, 'parse_request_trick' ));
        $controller = new LeadPageTypeController();
        add_action('wp', array($controller, 'initPage'));
    }



}