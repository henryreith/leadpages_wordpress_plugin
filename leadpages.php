<?php

/*
Plugin Name: Leadpages Connector
Plugin URI: http://leadpages.net
Description:Leadpages connector plugin
Version: 2.0.1
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

//hopefully deactive plugin if updated via admin and php version is less that 5.4
//this is ugly as we are now doing this twice here and on activation need to consolidate into one function
if ( version_compare( PHP_VERSION, 5.4, '<' ) ){
    $activePlugins = get_option('active_plugins', true);
    foreach($activePlugins as $key => $plugin){
        if($plugin == 'leadpages/leadpages.php'){
            unset($activePlugins[$key]);
        }
    }
    update_option('active_plugins', $activePlugins);
    wp_die('<p>The <strong>Leadpages&reg;</strong> plugin requires php version <strong> 5.4 </strong> or greater.</p> <p>You are currently using <strong>'.PHP_VERSION.'</strong></p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=>TRUE ) );

}

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

require $leadpagesConfig['basePath'] . 'Framework/ServiceContainer/ServiceContainer.php';


/*
  |--------------------------------------------------------------------------
  | Application Bootstrap
  |--------------------------------------------------------------------------
  |
  | Include bootstrap files to setup app
  |
  */


include('SetupFunctions.php');

if (is_admin() || is_network_admin()) {
    $adminBootstrap = $ioc['adminBootStrap'];
    include('App/Helpers/ErrorHandlerAjax.php');
}
if (!is_admin() && !is_network_admin()) {
    global $ioc;
    $frontBootstrap = $ioc['frontBootStrap'];

}


function getScreen()
{
    global $leadpagesConfig;

    $screen = get_current_screen();
    $leadpagesConfig['currentScreen'] = $screen->post_type;
}


add_action('current_screen', 'getScreen');
