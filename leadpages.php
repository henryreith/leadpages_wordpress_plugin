<?php

/*
Plugin Name: Leadpages Connector
Plugin URI: http://leadpages.net
Description:LeadPages connector plugin
Version: 2.0
Author: Leadpages
Author URI: http://leadpages.net
License: GPL2
*/

/**
 * Load plugin textdomain.
 */

use Leadpages\Admin\Providers\Update;

load_plugin_textdomain('leadpages', false,
  plugin_basename(dirname(__FILE__)) . '/App/Languages');

/*
  |--------------------------------------------------------------------------
  | Application Entry Point
  |--------------------------------------------------------------------------
  |
  | This will be your plugin entry point. This file should
  | not contain any logic for your plugin what so ever.
  |
  */

require('vendor/autoload.php');
require('App/Config/App.php');
/*
  |--------------------------------------------------------------------------
  | Bootstrap IOC Container
  |--------------------------------------------------------------------------
  |
  | This framework utilizes the Pimple IOC container from SensioLabs
  | Bootstrap the IOC container here. Documentation for container
  | can be found at http://pimple.sensiolabs.org/
  |
  */

require $config['basePath'] . 'Framework/ServiceContainer/ServiceContainer.php';


/*
  |--------------------------------------------------------------------------
  | Application Bootstrap
  |--------------------------------------------------------------------------
  |
  | Include bootstrap files to setup app
  |
  */




if (is_admin() || is_network_admin()) {
    $adminBootstrap = $ioc['adminBootStrap'];
    include('App/Helpers/ErrorHandlerAjax.php');
}
if (!is_admin() && !is_network_admin()) {
    global $ioc;
    $frontBootstrap = $ioc['frontBootStrap'];

}


//deactivation

function deactivateLeadpages(){
    delete_option('leadpages_security_token');
    setcookie("leadpagesLoginCookieGood", "", time()-3600);
}

register_deactivation_hook(__FILE__,'deactivateLeadpages');

//update all old slugs to match new structure


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
    if(isset($_COOKIE['leadpagesLoginCookieGood'])){
        setcookie('leadpagesLoginCookieGood', "", time()-3600);
        unset($_COOKIE['leadpagesLoginCookieGood']);
    }
    checkPHPVersion();

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

}

register_activation_hook(__FILE__, 'activateLeadpages');



