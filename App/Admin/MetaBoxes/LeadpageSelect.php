<?php


namespace Leadpages\admin\MetaBoxes;

use TheLoop\Contracts\MetaBox;
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

    public function callBack($object, $box)
    {
        ?>
            <select name="leadpages_my_selected_page" id="leadpages_my_selected_page">
                <option value="none">Select...</option>
                <?= $this->generateSelectList(); ?>
            </select>
        <?php
    }


    public function registerMetaBox()
    {
        add_action('add_meta_boxes', array($this, 'defineMetaBox'));
    }

    private function generateSelectList(){

        $items = $this->ioc['pagesApi']->getAllUserPages();
        $optionString = '';
        foreach($items['_items'] as $page){
            $optionString .= "<option value=\"{$page['id']}\">{$page['name']}</option>";
        }
        return $optionString;
    }
}