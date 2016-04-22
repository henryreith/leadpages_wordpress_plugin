<?php


namespace Leadpages\Helpers;

use Leadpages\Admin\Providers\LeadpagesPagesApi;

class IsLeadPage
{

    use LeadpageErrorHandlers;

    private $pagesApi;

    public function __construct(LeadpagesPagesApi $pagesApi)
    {
        $this->pagesApi = $pagesApi;
    }

    public static function checkByPost($post){
        if ($post->post_type != 'leadpages_post') {
            return false;
        }else{
            return true;
        }
    }

    public static function isLeadPageUrlQuery(){
        global $wpdb;
        $current = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $prefix  = $wpdb->prefix;
        $query   = "SELECT post_id FROM {$prefix}postmeta where meta_value = '{$current}'";
        $result  = $wpdb->get_row($query);
        if (empty($result)) {
            //try to grab page from old slug if not a current page
            $post = get_post();
            $postName = get_query_var('pagename');
            $query   = "SELECT ID, post_type FROM {$prefix}posts where post_name = '{$postName}'";
            $result = $wpdb->get_row($query);
            if($result->post_type == 'leadpages_post'){
                return get_post($result->ID);
            }
        }
        if(empty($result)){
            return;
        }
        return get_post($result->post_id);
    }



}