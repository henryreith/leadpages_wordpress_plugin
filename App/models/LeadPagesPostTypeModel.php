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
        //echo '<pre>';print_r($post);die();

        // check if this is our type
        IsLeadPage::checkByPost($post, $post_id);

        //save slug for backwards compatibiltiy
        $permalink = get_permalink($post_id);
        update_post_meta($post_id, 'leadpages_slug', $permalink);

        //check to see if this page type already exists
        //save html for page
        $html = $this->getLeadPageHtml();
        update_post_meta($post_id, 'leadpages_my_selected_page', $html);

        //save post name in meta for backwards compatibility
        update_post_meta($post_id, 'leadpages_name', $post->post_name);

        update_post_meta($post_id, 'leadpages_page_id', $this->LeadPageId);

        $postType = sanitize_text_field($_POST['leadpages-post-type']);
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

        if ($post->post_status == 'publish') {
            $this->removePageType($post_id, $postType);
            $this->saveLeadPageOptions($post_id, $postType);
        }
    }


    public function saveLeadPageOptions($post_id, $postType)
    {
        switch ($postType) {
            case 'lp':
                //echo 'Normal';
                break;
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
        //add_filter('wp_insert_post_data', array($this, 'checkPostTypes'), 10, 2);
        add_action('edit_post', array($this, 'saveLeadPageMeta'), 10, 2);
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
}