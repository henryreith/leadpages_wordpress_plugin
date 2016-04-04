<?php


namespace Leadpages\models;

use Leadpages\Admin\CustomPostTypes\LeadpagesPostType;
use Leadpages\helpers\IsLeadPage;
use Leadpages\Helpers\LeadpageType;
use Leadpages\Helpers\LeadpageErrorHandlers;
use Leadpages\Admin\Providers\LeadpagesPagesApi;

class LeadPagesPostTypeModel
{
    protected $html;
    /**
     * @var
     */
    private $PagesApi;
    public $LeadPageId;
    /**
     * @var \Leadpages\Admin\CustomPostTypes\LeadpagesPostType
     */
    private $postType;

    public function __construct(LeadpagesPagesApi $pagesApi, LeadpagesPostType $postType)
    {
        $this->PagesApi = $pagesApi;
        $this->postType = $postType;
    }

    public function saveLeadPageMeta($post_id, $post)
    {
        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // check if this is our type
        $isLeadPage = IsLeadPage::checkByPost($post, $post_id);
        if(!$isLeadPage){
            return $post_id;
        }
        if($post->post_status = "trash" && !isset($_POST['post_status'])){
            $this->deletePost($post_id);
            return $post_id;
        }

        //setup all vars for inserting or deleting posts
        $permalink = get_permalink($post_id);
        $postType = sanitize_text_field($_POST['leadpages-post-type']);
        $this->LeadPageId = sanitize_text_field($_POST['leadpages_my_selected_page']);



        //set cache
        if(isset($_POST['cache_this'])){
            update_post_meta($post_id, 'cache_page', 'true');
        }else{
            update_post_meta($post_id, 'cache_page', 'false');
        }

        update_post_meta($post_id, 'leadpages_slug', $permalink);

        //save post name in meta for backwards compatibility
        update_post_meta($post_id, 'leadpages_name', $post->post_name);

        update_post_meta($post_id, 'leadpages_page_id', $this->LeadPageId);

        update_post_meta($post_id, 'leadpages_post_type', $postType);
        //TODO add in split test when its avaiable



        //return here as if there was an error saving the page because the type
        //already exists (ErrorHandlerAjax.php on page save)
        $error = $this->postType->checkError($postType, $post_id);

        if($error == 'error'){
            return;
        }
        /**
         * only update these items if the post is actually being published
         */
        //echo '<pre>'; print_r($_POST);die();
        if ($_POST['post_status'] == 'publish') {
            $this->removePageType($post_id, $postType);
            $this->saveLeadPageOptions($post_id, $postType);
        }
    }


    public function saveLeadPageOptions($post_id, $postType)
    {
        switch ($postType) {
            case 'fp':
                LeadpageType::set_front_lead_page($post_id);
                break;
            case 'wg':
                LeadpageType::set_wg_lead_page($post_id);
                break;
            case 'nf':
                LeadpageType::set_404_lead_page($post_id);
                break;
        }
    }


    public function checkPostTypes($postId, $post)
    {
        $post = (object)$post;

        if ($post->post_status == 'trash' || $post->post_status == 'auto-draft') {
            return;
        }
        $post->ID = $postId;

        $postType = sanitize_text_field($_POST['leadpages-post-type']);
        $error    = LeadpageErrorHandlers::checkPageTypeExists($postType, $post);
        if ($error) {
            $post->post_status = 'draft';
            return $post;
        }

    }


    public function deletePost($post_id){
        global $wpdb;
        $postType = $this->getMetaPageType($post_id);
        $tablePrefix =  $wpdb->base_prefix;
        $wpdb->delete( $tablePrefix.'postmeta', array( 'post_id' => $post_id ) );
        if ($postType == 'fp') {
            delete_option('leadpages_front_page_id');
        }
        if ($postType ==  'wg') {
            delete_option('leadpages_wg_page_id');
        }
        if ($postType ==  'nf') {
            delete_option('leadpages_404_page_id');
        }
    }

    /**
     * get the id of every special page type, then check the post id being saved
     * and if it matches the id of one of the page type but isnt being saved
     * as that page type, we need to delete that page type as it no longer exists
     *
     * @param $post_id
     * @param $postType
     */
    public function removePageType($post_id, $postType)
    {
        $frontpage   = LeadpageType::get_front_lead_page();
        $welcomeGate = LeadpageType::get_wg_lead_page();
        $nf          = LeadpageType::get_404_lead_page();


        if ($post_id == $frontpage && $postType != 'fp') {
            delete_option('leadpages_front_page_id');
        }
        if ($post_id == $welcomeGate && $postType != 'wg') {
            delete_option('leadpages_wg_page_id');
        }
        if ($post_id == $nf && $postType != 'nf') {
            delete_option('leadpages_404_page_id');
        }
    }

    public function getLeadPageHtml()
    {
        if (isset($_POST['leadpages_my_selected_page'])) {
            $this->LeadPageId = sanitize_text_field($_POST['leadpages_my_selected_page']);
            $html             = $this->PagesApi->downloadPageHtml($this->LeadPageId);
            return $html;
        }
    }

    public function save()
    {
        add_action('edit_post', array($this, 'saveLeadPageMeta'), 999, 2);

    }

    public static function getMetaPageType($post_id)
    {
        $meta = get_post_meta($post_id, 'leadpages_post_type');
        if (sizeof($meta) == 0) {
            return false;
        } else {
            return $meta[0];
        }
    }

    public static function getMetaPageId($post_id)
    {
        $meta = get_post_meta($post_id, 'leadpages_page_id');
        if (sizeof($meta) == 0) {
            return false;
        } else {
            return $meta[0];
        }
    }

    public static function getMetaPagePath($post_id)
    {
        $meta = get_post_meta($post_id, 'leadpages_slug');
        if (sizeof($meta) == 0) {
            return false;
        } else {
            return $meta[0];
        }
    }

    public static function getMetaCache($post_id){
        $meta = get_post_meta($post_id, 'cache_page');
        if (sizeof($meta) == 0) {
            return false;
        } else {
            return $meta[0];
        }
    }

    public function setCacheForPage($pageId, $html){
        set_transient('leadpages_page_html_cache_'.$pageId, $html, 600);
    }

    public function getCacheForPage($pageId){
       return get_transient('leadpages_page_html_cache_'.$pageId);
    }

    public function getHtml($pageId){
        //check to see if we need to return cached version
        $LeadpageId = get_post_meta($pageId, 'leadpages_page_id', true);
        $getCache = get_post_meta($pageId, 'cache_page', true);
        if($getCache == 'true'){
            //check if cache exist
            $currentCache = $this->getCacheForPage($LeadpageId);
            if($currentCache){
                echo $currentCache;die();
            }else{
                //if no cache get the html then set the cache for next time
                //then return html
                $html = $this->PagesApi->downloadPageHtml($LeadpageId);
                $this->setCacheForPage($LeadpageId, $html);
                echo $html; die();
            }
        }

        //if we don't fall into cache just echo $html
        $html = $this->PagesApi->downloadPageHtml($LeadpageId);
        echo $html; die();

    }
}