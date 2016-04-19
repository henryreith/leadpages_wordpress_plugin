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
            return false;
        }
        return get_post($result->post_id);
    }



}