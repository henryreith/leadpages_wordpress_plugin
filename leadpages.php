<?php

/*
Plugin Name: Leadpages Connector
Plugin URI: http://leadpages.net
Description: A brief description of the Plugin.
Version: 2.0
Author: Leadpages
Author URI: http://leadpages.net
License: GPL2
*/

use Leadpages\Admin\CustomPostTypes\LeadpagesPostType;

include 'c3.php';

/**
 * Load plugin textdomain.
 */

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



