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



}