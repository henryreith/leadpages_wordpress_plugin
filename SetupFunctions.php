<?php
function deactivateLeadpages(){
    // delete_option('leadpages_security_token');
    //setcookie("leadpagesLoginCookieGood", "", time()-3600);
}

register_deactivation_hook(__FILE__,'deactivateLeadpages');

//update all old slugs to match new structure

//refresh page after user updates plugin to refresh the left hand menu
add_action( 'user_meta_after_user_update', 'refreshPage' );
function refreshPage() {
    echo '<script>location.reload();</script>';
}

function checkPHPVersion()
{
    $php = '5.4';
    if ( version_compare( PHP_VERSION, $php, '<' ) )
        $flag = 'PHP';
    else
        return;
    deactivate_plugins( basename( __FILE__ ) );
    wp_die('<p>The <strong>Leadpages&reg;</strong> plugin requires php version <strong>'.$php.'</strong> or greater.</p> <p>You are currently using <strong>'.PHP_VERSION.'</strong></p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );

}

function my_error_notice() {
    ?>
    <div class="error notice">
        <p><?php _e( 'There has been an error. Bummer!', 'my_plugin_textdomain' ); ?></p>
    </div>
    <?php
}

function leadpages_deactivate_self() {
    deactivate_plugins( plugin_basename( __FILE__ ) );
}

function activateLeadpages(){
    /*if(isset($_COOKIE['leadpagesLoginCookieGood'])){
        setcookie('leadpagesLoginCookieGood', "", time()-3600);
        unset($_COOKIE['leadpagesLoginCookieGood']);
    }*/
    checkPHPVersion();

}

function updateToVersion2x(){
    $dbHasBeenUpdated = get_option('leadpages_version2_update');
    if($dbHasBeenUpdated){
        return;
    }
    //update old plugin info to work with new plugin
    global $wpdb;

    $prefix = $wpdb->prefix;
    //update urls in options table
    $results = $wpdb->get_results( "SELECT * FROM {$prefix}posts WHERE post_type = 'leadpages_post'", OBJECT );

    foreach($results as $leadpage){
        $ID = $leadpage->ID;
        $newUrl = get_site_url().$leadpage->post_title;
        update_post_meta($ID, 'leadpages_slug', $newUrl);

    }

    foreach($results as $leadpage){
        $newTitle = implode('', explode('/', $leadpage->post_title, 2));
        $wpdb->update($prefix.'posts', array('post_title' => $newTitle), array('ID' => $ID));
    }

    //update leadbox settings to match new plugin
    $lp_settings = get_option('lp_settings');
    if($lp_settings['leadboxes_timed_display_radio'] == 'posts'){
        $lp_settings['leadboxes_timed_display_radio'] ='post';
    }
    if($lp_settings['leadboxes_exit_display_radio'] == 'posts'){
        $lp_settings['leadboxes_exit_display_radio'] = 'post';
    }

    update_option('lp_settings', $lp_settings);
    update_option('leadpages_version2_update', true);
}

/**
 *Need to refresh on update to refresh menu
 */
function refreshPageOnUpdateToVersion2x(){
    $pageHasBeenRefreshed = get_option('leadpages_version2_refresh');
    if($pageHasBeenRefreshed){
        return;
    }
    update_option('leadpages_version2_refresh', true);
    header("Refresh:0");

}

refreshPageOnUpdateToVersion2x();
updateToVersion2x();

register_activation_hook(__FILE__, 'activateLeadpages');