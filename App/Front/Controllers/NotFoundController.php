<?php

namespace LeadpagesWP\Front\Controllers;

use Leadpages\Pages\LeadpagesPages;
use LeadpagesWP\Helpers\LeadpageType;
use LeadpagesWP\models\LeadPagesPostTypeModel;

class NotFoundController
{

    protected $nfPageId;
    protected $nfPageUrl;
    /**
     * @var
     */
    private $postTypeModel;
    /**
     * @var \Leadpages\Pages\LeadpagesPages
     */
    private $pagesApi;

    public function __construct(LeadPagesPostTypeModel $postTypeModel, LeadpagesPages $pagesApi)
    {
        $this->postTypeModel = $postTypeModel;
        $this->pagesApi = $pagesApi;
    }

    protected function nfPageExists()
    {
        $this->nfPageId = LeadpageType::get_404_lead_page();
        if (!$this->nfPageId) {
            return false;
        }

        return true;
    }

    protected function nfPageUrl(){
        $this->nfPageUrl = get_post_meta($this->nfPageId, 'leadpages_slug', true);
    }

    public function displaynfPage()
    {
        if($this->nfPageExists() && is_404()){
            $pageID = $this->postTypeModel->getLeadpagePageId($this->nfPageId);
            $html = $this->pagesApi->downloadPageHtml($pageID);
            if(ob_get_length() > 0){
                ob_clean();
            }
            ob_start();//start output buffer
            status_header( '404' );
            print $html;
            ob_end_flush();
            die();

        }
    }

}