<?php


namespace Leadpages\Helpers;

use Leadpages\Admin\Providers\LeadpagesPagesApi;

class IsLeadPage
{

    private $pagesApi;

    public function __construct(LeadpagesPagesApi $pagesApi)
    {
        $this->pagesApi = $pagesApi;
    }

    public function isLeadPageAndDisplayHtml()
    {
        $obj = get_queried_object();
        if($obj->post_type == 'leadpages_post'){
            $pageID = get_post_meta($obj->ID, 'leadpages_page_id');
            $pageID = $pageID[0];
            //TODO check if is split tested and if so dont use cache version
            //non cache version
            $html = $this->pagesApi->downloadPageHtml($pageID);
            echo $html; die();

            //cached version
            //$html = get_post_meta($obj->ID, 'leadpages_my_selected_page');
            //return $html[0];

        }
    }

    public function checkDislayHtml(){
        add_filter( 'wp', array($this, 'isLeadPageAndDisplayHtml'));
    }

    public static function checkByPost($post, $post_id){
        if ($post->post_type != 'leadpages_post') {
            return $post_id;
        }
    }

}