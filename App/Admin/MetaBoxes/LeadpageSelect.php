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
        ?>
            <select name="leadpages_my_selected_page" id="leadpages_my_selected_page">
                <option value="none">Select...</option>
                <?= $this->generateSelectList($post); ?>
            </select>
        <?php
    }


    public function registerMetaBox()
    {
        add_action('add_meta_boxes', array($this, 'defineMetaBox'));
    }

    private function generateSelectList($post){
        $currentPage = LeadPagesPostTypeModel::getMetaPageId($post->ID);
        $items = $this->ioc['pagesApi']->getAllUserPages();
        $optionString = '';
        foreach($items['_items'] as $page){
            $optionString .= "<option value=\"{$page['id']}\" ". ($currentPage == $page['id'] ? 'selected="selected"' : '')." >{$page['name']}</option>";
        }
        return $optionString;
    }
}