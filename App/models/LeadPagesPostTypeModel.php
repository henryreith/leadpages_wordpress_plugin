<?php


namespace LeadpagesWP\models;

use Leadpages\Pages\LeadpagesPages;
use LeadpagesWP\Helpers\LeadpageType;
use LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType;

class LeadPagesPostTypeModel
{
    protected $html;
    /**
     * @var
     */
    private $PagesApi;
    public $LeadPageId;
    public $LeadpageXORId;
    /**
     * @var \LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType
     */
    private $postType;

    public function __construct(LeadpagesPages $pagesApi, LeadpagesPostType $postType)
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
        //check if its a leadpage
        if($post->post_type != 'leadpages_post') return $post_id;

        //check to see if the status is trash if so delete it and return post_id
        if ($post->post_status = "trash" && !isset($_POST['post_status'])) {
            $this->deletePost($post_id);
            return $post_id;
        }

        //setup all vars for inserting or deleting posts
        $postType  = sanitize_text_field($_POST['leadpages-post-type']);

        //maybe a better way to do this? but sending the xor_id as the # before : and leadpage id after :
        //so $ids[0] is the xor id for backward compatibility and $ids[1] is the leadpage id
        $ids = explode(':', $_POST['leadpages_my_selected_page']);

        $this->LeadPageId    = sanitize_text_field($ids[1]);
        $this->LeadpageXORId = sanitize_text_field($ids[0]);


        //set cache
        if (isset($_POST['cache_this']) && $_POST['cache_this'] == "true") {
            update_post_meta($post_id, 'cache_page', 'true');
            $this->setCacheForPage($this->LeadPageId);

        } elseif (isset($_POST['cache_this']) && $_POST['cache_this'] == "false") {
            update_post_meta($post_id, 'cache_page', 'false');
        } else {

        }

        update_post_meta($post_id, 'leadpages_slug', sanitize_text_field($_POST['leadpages_slug']));

        //save post name in meta for backwards compatibility
        update_post_meta($post_id, 'leadpages_name', sanitize_text_field($_POST['leadpages_name']));

        update_post_meta($post_id, 'leadpages_page_id', $this->LeadPageId);
        update_post_meta($post_id, 'leadpages_my_selected_page', $this->LeadpageXORId);

        update_post_meta($post_id, 'leadpages_post_type', $postType);

        //return here as if there was an error saving the page because the type
        //already exists (ErrorHandlerAjax.php on page save)

//        $error = $this->postType->checkError($postType, $post_id);
//
//        if ($error == 'error') {
//            return $post_id;
//        }
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


    public function deletePost($post_id)
    {
        global $wpdb;
        $postType    = $this->getMetaPageType($post_id);
        $tablePrefix = $wpdb->base_prefix;
        $wpdb->delete($tablePrefix . 'postmeta', array('post_id' => $post_id));
        if ($postType == 'fp') {
            delete_option('leadpages_front_page_id');
        }
        if ($postType == 'wg') {
            delete_option('leadpages_wg_page_id');
        }
        if ($postType == 'nf') {
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
        add_action( 'save_post', array( &$this, 'custom_post_type_title' ), 10 );
    }


    public static function getMetaPageType($post_id)
    {
        $meta = get_post_meta($post_id, 'leadpages_post_type');
        if (sizeof($meta) == 0) {
            $meta = static::getTypeFromOptions($post_id);
	    //would be empty if its just alp ad those types were not saved as a type in old plugin
            if(empty($meta)){
                return 'lp';
            }
            return $meta;
        } else {
            return $meta[0];
        }
    }

    public static function getTypeFromOptions($postId)
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $query = "SELECT option_value, option_name from {$prefix}options where option_name in ('leadpages_front_page_id', 'leadpages_wg_page_id', 'leadpages_404_page_id')";
	
        $results = $wpdb->get_results($query, 'ARRAY_A');
        foreach($results as $result){
            if($result['option_value'] == $postId){
                switch($result['option_name']){
                    case('leadpages_front_page_id'):
                        return 'fp';
                        break;
                    case('leadpages_wg_page_id'):
                        return 'wg';
                        break;
                    case('leadpages_404_page_id'):
                        return 'nf';
                        break;
                }
            }
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
        } else{
            return $meta[0];
        }
    }

    public static function getMetaCache($post_id)
    {
        $meta = get_post_meta($post_id, 'cache_page');
        if (sizeof($meta) == 0) {
            return false;
        } else {
            return $meta[0];
        }
    }

    public function setCacheForPage($pageId)
    {
        $apiResponse = $this->PagesApi->downloadPageHtml($pageId);
        $html = $apiResponse['response'];
        set_transient('leadpages_page_html_cache_' . $pageId, $html, 60*60*24);//one day/24 hours
    }

    public function getCacheForPage($pageId)
    {
        return get_transient('leadpages_page_html_cache_' . $pageId);
    }

    public function getLeadpagePageId($pageId){
        $LeadpageId = get_post_meta($pageId, 'leadpages_page_id', true);

        //if $LeadpageId does not exist try to get it from the xor id...once again maybe something that should be done
        if (empty($LeadpageId)) {
            $LeadpageId = $this->getPageByXORId($pageId);
        }
        if($LeadpageId == false){
            return false;
        }

        return $LeadpageId;
    }

    public function getHtml($pageId)
    {
        //try to get leadpages_page_id if it is set(version 2 plugin pages)
        $LeadpageId = get_post_meta($pageId, 'leadpages_page_id', true);

        //if $LeadpageId does not exist try to get it from the xor id...once again maybe something that should be done
        //TODO in the api like getPageByXor call
        if (empty($LeadpageId)) {
            $LeadpageId = $this->getPageByXORId($pageId);
        }
        if($LeadpageId == false){
            return false;
        }
        $getCache = get_post_meta($pageId, 'cache_page', true);
        if ($getCache == 'true') {
            //check if cache exist
            $currentCache = $this->getCacheForPage($LeadpageId);
            if ($currentCache) {
                echo $currentCache;
                die();
            } else {
                //if no cache get the html then set the cache for next time
                //then return html
                $html = $this->PagesApi->downloadPageHtml($LeadpageId);
                $this->setCacheForPage($LeadpageId, $html);
                echo $html;
                die();
            }
        }
        //if we don't fall into cache just echo $html
        $html = $this->PagesApi->downloadPageHtml($LeadpageId);
        echo $html;
        die();
    }

    public function getPageByXORId($pageId, $xorId = '')
    {

        if(empty($xorId)){
            $xorId = get_post_meta($pageId, 'leadpages_my_selected_page', true);
        }

        if(empty($xorId)) return false;

        $pages = $this->PagesApi->getAllUserPages();
        foreach ($pages['_items'] as $page) {
            if ($page['_meta']['xor_hex_id'] == $xorId) {
                return $page['_meta']['id'];
            }
        }
        //return false if page doesn't exist
        return false;
    }

    /**
     * Update Post title to be the slug of the page
     *
     * @param $post_id
     */
    function custom_post_type_title( $post_id ) {
        global $wpdb, $post_type;
        if ( 'leadpages_post' == $post_type ) {
            $slug  = get_post_meta( $post_id, 'leadpages_slug', true );
            $where = array( 'ID' => $post_id );
            $wpdb->update( $wpdb->posts, array( 'post_title' => $slug, 'post_name' => $slug ), $where );
        }
    }

    public static function get_all_posts($requestedPage)
    {
        global $wpdb;
        $query = $wpdb->prepare( "
            SELECT
            pm.*,
            p.ID
            from {$wpdb->prefix}postmeta as pm
            INNER JOIN {$wpdb->prefix}posts as p
            on p.ID = pm.post_id
            where p.post_name = '%s'
            ", [$requestedPage]
        );

        $result = $wpdb->get_results($query, ARRAY_A);

        if(empty($result)) return false;

        $lpPostArray = [];
        foreach($result as $post){
            $lpPostArray[$post['meta_key']] = $post['meta_value'];
            $lpPostArray['post_id'] = $post['ID'];
        }
        return $lpPostArray;
    }
}