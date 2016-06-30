<?php


namespace LeadpagesWP\Admin\MetaBoxes;

use TheLoop\Contracts\MetaBox;
use LeadpagesWP\models\LeadPagesPostTypeModel;
use LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType;
use Leadpages\Pages\LeadpagesPages;


class LeadpageSelect extends LeadpagesPostType implements MetaBox
{

    /**
     * @var \LeadpagesWP\models\LeadPagesPostTypeModel
     */
    private $postTypeModel;
    /**
     * @var \Leadpages\Pages\LeadpagesPages
     */
    private $pagesApi;

    public function __construct()
    {
        global $leadpagesApp;

        $this->pagesApi = $leadpagesApp['pagesApi'];
        $this->postTypeModel = $leadpagesApp['lpPostTypeModel'];
        add_action( 'wp_ajax_get_pages_dropdown', array($this, 'generateSelectList') );
        add_action( 'wp_ajax_nopriv_get_pages_dropdown', array($this, 'generateSelectList') );

    }

    public static function getName(){
        return get_called_class();
    }

    public function defineMetaBox()
    {
        add_meta_box("leadpage-select", "Select Leadpage ", array($this, 'callback'), $this->postTypeName, "normal", "high", null);
    }

    public function callBack($post, $box)
    {
        $useCache = LeadPagesPostTypeModel::getMetaCache($post->ID);

        ?>
        <div class="leadpagesSelect">
            <input type="hidden" name="leadpages_my_selected_page">

            <select name="leadpages_my_selected_page" id="leadpages_my_selected_page">
                <option value="none">Select...</option>
            </select>
            <br />
            <br />
            <label for="cache_this">Cache this page?</label>
            <br />
            <br />

            <input type="radio" id="cache_this_true" name="cache_this" value="true"  <?php echo ($useCache == 'true') ? 'checked="checked"': ''; ?>> Yes, cache for improved performance. <br />
            <input type="radio" id="cache_this_false" name="cache_this" value="false"  <?php echo ($useCache != 'true') ? 'checked="checked"': ''; ?>> No, re-fetch on each visit; slower, but required for split testing.
        </div>
        <?php
    }


    public function registerMetaBox()
    {
        add_action('add_meta_boxes', array($this, 'defineMetaBox'));
    }

    public function generateSelectList(){
        global $leadpagesApp;

        $id = sanitize_text_field($_POST['id']);
        $currentPage = LeadPagesPostTypeModel::getMetaPageId($id);

        if(!$currentPage){
            $currentPage = $leadpagesApp['lpPostTypeModel']->getPageByXORId($id);
        }

       $items = $leadpagesApp['pagesApi']->getAllUserPages();
        $optionString = '';
        foreach($items['_items'] as $page){
            $optionString .= "<option value=\"{$page['_meta']['xor_hex_id']}:{$page['id']}\" ". ($currentPage == $page['id'] ? 'selected="selected"' : '')." >{$page['name']}</option>";
        }
        echo $optionString;
        die();
    }


}