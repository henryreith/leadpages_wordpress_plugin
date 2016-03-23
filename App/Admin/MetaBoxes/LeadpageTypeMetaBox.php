<?php

namespace Leadpages\admin\MetaBoxes;

use TheLoop\Contracts\MetaBox;
use Leadpages\Admin\CustomPostTypes\LeadpagesPostType;



class LeadpageTypeMetaBox extends LeadpagesPostType implements MetaBox {

    public function defineMetaBox(){
        add_meta_box("leadpage-type", "Select Page Type", array($this, 'callback'), $this->postTypeName, "normal", "high", null);
    }

    public function callBack($object, $box){

        ?>
            <select name="leadpages-post-type" id="leadpageType">
                <option value="none">Select...</option>
                <option value="lp">Normal Page</option>
                <option value="fp">Home Page</option>
                <option value="wg">Welcome Gate&trade;</option>
                <option value="nf">404 Page</option>
            </select>
        <?php
    }

    public function registerMetaBox(){
        add_action('add_meta_boxes', array($this, 'defineMetaBox'));
    }

}