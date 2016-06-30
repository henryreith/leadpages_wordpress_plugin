<?php


namespace LeadpagesWP\Admin\MetaBoxes;

use TheLoop\Contracts\MetaBox;
use LeadpagesWP\models\LeadPagesPostTypeModel;
use LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType;
use Leadpages\Pages\LeadpagesPages;


class LeadpageSlug extends LeadpagesPostType implements MetaBox
{

    public static function getName(){
        return get_called_class();
    }

    public function defineMetaBox()
    {
        add_meta_box("leadpage-slug", "Leadpage Slug", array($this, 'callback'), $this->postTypeName, "normal", "high", null);
    }

    public function callBack($post, $box)
    {
        $slug = LeadPagesPostTypeModel::getMetaPagePath($post->ID);
        ?>
        <div class="ui-loading">
            <div class="ui-loading__dots ui-loading__dots--1"></div>
            <div class="ui-loading__dots ui-loading__dots--2"></div>
            <div class="ui-loading__dots ui-loading__dots--3"></div>
        </div>
        <div class="leadpagesSlug">
            <input type="text" id="leadpages_slug_input" name="leadpages_slug" value="<?php echo $slug; ?>">
        </div>
        <?php
    }


    public function registerMetaBox()
    {
        add_action('add_meta_boxes', array($this, 'defineMetaBox'));
    }

}