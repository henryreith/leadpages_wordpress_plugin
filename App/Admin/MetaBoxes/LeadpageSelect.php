<?php


namespace Leadpages\admin\MetaBoxes;

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

    public function defineMetaBox()
    {
        add_meta_box("leadpage-select", "Select Leadpage ", array($this, 'callback'), $this->postTypeName, "normal", "high", null);
    }

    public function callBack($post, $box)
    {
        $useCache = LeadPagesPostTypeModel::getMetaCache($post->ID);
        //$this->generateSelectList($post); //here for testing
        ?>
            <select name="leadpages_my_selected_page" id="leadpages_my_selected_page">
                <option value="none">Select...</option>
                <?= $this->generateSelectList($post); ?>
            </select>
        <br />
        <br />
        <label for="cache_this">Cache this page?</label>
        <input type="checkbox" name="cache_this" value="true" <?php echo ($useCache == 'true' ? 'checked="checked"': ''); ?> >
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