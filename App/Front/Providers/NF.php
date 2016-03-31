<?php


namespace Leadpages\Front\Providers;

use Leadpages\Helpers\LeadpageType;

class NF
{

    protected $nfPageId;
    protected $nfPageUrl;

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

    public function displaynfPage($ioc)
    {
        if($this->nfPageExists() && is_404()){
            $pageID = get_post_meta($this->nfPageId, 'leadpages_page_id', true);
            //TODO check if is split tested and if so dont use cache version
            //non cache version
            $html = $ioc['pagesApi']->downloadPageHtml($pageID);
            status_header( '404' );
            echo $html; die();
        }
    }

}