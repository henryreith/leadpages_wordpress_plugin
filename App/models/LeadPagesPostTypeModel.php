<?php


namespace Leadpages\models;

use Leadpages\helpers\IsLeadPage;
use Leadpages\Admin\Providers\LeadpagesPagesApi;

class LeadPagesPostTypeModel
{
    protected $html;
    /**
     * @var
     */
    private $PagesApi;
    public  $LeadPageId;

    public function __construct(LeadpagesPagesApi $pagesApi)
    {
        $this->PagesApi = $pagesApi;
    }

    function saveLeadPageMeta( $post_id, $post)
    {
        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        // check if this is our type
        IsLeadPage::checkByPost($post, $post_id);

        //save html for page
        $html = $this->getLeadPageHtml();
        update_post_meta( $post_id, 'leadpages_my_selected_page', $html );

        //save post name in meta for backwards compatibility
        update_post_meta( $post_id, 'leadpages_name', $post->post_name );
        //save slug for backwards compatibiltiy
        $permalink = get_permalink($post_id);
        update_post_meta( $post_id, 'leadpages_slug', $permalink );
        update_post_meta( $post_id, 'leadpages_page_id', $this->LeadPageId);
        update_post_meta($post_id, 'leadpages_post_type', sanitize_text_field($_POST['leadpages-post-type']));
        //TODO add in split test when its avaiable
    }

    public function getLeadPageHtml(){
        if(isset($_POST['leadpages_my_selected_page'])) {
            $this->LeadPageId = sanitize_text_field($_POST['leadpages_my_selected_page']);
            $html             = $this->PagesApi->downloadPageHtml($this->LeadPageId);
            return $html;
        }
    }

    public function saveMeta(){
        add_action( 'edit_post', array($this, 'saveLeadPageMeta'), 10, 2 );
    }

    public static function getMetaPageType($post_id){
        $meta = get_post_meta($post_id, 'leadpages_post_type');
        if(sizeof($meta) == 0) {
            return false;
        }else{
            return $meta[0];
        }
    }

    public static function getMetaPageId($post_id){
        $meta = get_post_meta($post_id, 'leadpages_page_id');
        if(sizeof($meta) == 0) {
            return false;
        }else{
            return $meta[0];
        }
    }
}