<?php

namespace LeadpagesWP\Lib;

class AdminNotices
{

    public static function getName(){
        return get_called_class();
    }

    public static function NotLoggedInToLeadpages(){
        $loginUrl = admin_url()."?page=Leadpages";
        $message = <<<BOM
        <p>You are not logged into Leadpages. Your pages will not work until you login</p>
        <a href={$loginUrl}>Login to Leadpages</a>
BOM;


        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e( $message, 'leadpages' ); ?></p>
        </div>
        <?php
    }



}