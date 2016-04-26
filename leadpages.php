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

function activateLeadpages(){

    //register activation cron
    global $ioc;
    $ioc['update']->register_auto_update();


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



