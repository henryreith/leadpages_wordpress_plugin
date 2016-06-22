<?php

namespace LeadpagesWP\Admin\MetaBoxes;

use TheLoop\Contracts\MetaBox;
use LeadpagesWP\models\LeadPagesPostTypeModel;
use LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType;


class LeadpageType extends LeadpagesPostType implements MetaBox {

    public static function getName(){
        return get_called_class();
    }

    public function defineMetaBox(){
        add_meta_box("leadpage-type", "Select Page Type", array($this, 'callback'), $this->postTypeName, "normal", "high", null);
    }

    public function callBack($post, $box){
        $currentType = LeadPagesPostTypeModel::getMetaPageType($post->ID);
        ?>
        <div class="leadpageType">
            <select name="leadpages-post-type" id="leadpageType">
                <option value="none">Select...</option>
                <option value="lp" <?php echo $currentType == "lp" ? 'selected="selected"' : ""?> >Normal Page</option>
                <option value="fp" <?php echo $currentType == "fp" ? 'selected="selected"' : ""?> >Home Page</option>
                <option value="wg" <?php echo $currentType == "wg" ? 'selected="selected"' : ""?> >Welcome Gate&trade;</option>
                <option value="nf" <?php echo $currentType == "nf" ? 'selected="selected"' : ""?> >404 Page</option>
            </select>
        </div>
        <?php
    }

    public function registerMetaBox(){
        add_action('add_meta_boxes', array($this, 'defineMetaBox'));
    }

}