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
            $pageId = $this->postTypeModel->getLeadpagePageId($this->nfPageId);

            //check for cache
            $getCache = get_post_meta($this->nfPageId, 'cache_page', true);
            if($getCache == true){
                $html = $this->postTypeModel->getCacheForPage($pageId);
                if(empty($html)){
                    $html = $this->pagesApi->downloadPageHtml($pageId);
                    $this->postTypeModel->setCacheForPage($pageId);
                }
            }else {
                //no cache download html
                $html = $this->pagesApi->downloadPageHtml($pageId);
            }

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