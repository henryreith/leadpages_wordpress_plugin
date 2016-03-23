<?php

namespace Leadpages\Bootstrap;

use Leadpages\Helpers\IsLeadPage;
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

    public function __construct(LeadpagesPagesApi $pageApi) {

        $this->pagesApi   = $pageApi;
        $this->initFront();
    }

    public function initFront()
    {

        CustomPostType::create(LeadpagesPostType::class);

        $isLeadPage = new IsLeadPage($this->pagesApi);
        $isLeadPage->checkDislayHtml();
    }
}