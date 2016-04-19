<?php


namespace Leadpages\Admin\MetaBoxes;

use TheLoop\Contracts\MetaBox;
use Leadpages\models\LeadPagesPostTypeModel;
use TheLoop\ServiceContainer\ServiceContainerTrait;
use Leadpages\Admin\CustomPostTypes\LeadpagesPostType;


class LeadpageSelect extends LeadpagesPostType implements MetaBox
{
    use ServiceContainerTrait;

    private $pagesApi;
    private $ioc;

    public function __construct()
    {
        $this->ioc = $this->getContainer();

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


        $this->generateSelectList($post); //here for testing
        ?>
            <select name="leadpages_my_selected_page" id="leadpages_my_selected_page">
                <option value="none">Select...</option>
                <?= $this->generateSelectList($post); ?>
            </select>
        <br />
        <br />
        <label for="cache_this">Cache this page?</label>
        <br />
        <br />

        <input type="radio" name="cache_this" value="true"  <?php echo ($useCache == 'true') ? 'checked="checked"': ''; ?>> Yes, cache for improved performance. <br />
        <input type="radio" name="cache_this" value="false"  <?php echo ($useCache != 'true') ? 'checked="checked"': ''; ?>> No, re-fetch on each visit; slower, but required for split testing.
        <?php
    }


    public function registerMetaBox()
    {
        add_action('add_meta_boxes', array($this, 'defineMetaBox'));
    }

    private function generateSelectList($post){
        $currentPage = LeadPagesPostTypeModel::getMetaPageId($post->ID);
        $items = $this->ioc['pagesApi']->stripB3NonPublished();
        $optionString = '';
        foreach($items['_items'] as $page){
            $optionString .= "<option value=\"{$page['id']}\" ". ($currentPage == $page['id'] ? 'selected="selected"' : '')." >{$page['name']}</option>";
        }
        return $optionString;
    }
}