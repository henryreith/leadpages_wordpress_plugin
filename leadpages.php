<?php

/*
Plugin Name: Leadpages Connector
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description:
Author: Leadpages
Version: 2.0
Author URI: http://leadpages.net
*/


/*
  |--------------------------------------------------------------------------
  | Application Entry Point
  |--------------------------------------------------------------------------
  |
  | This will be your plugin entry point. This file should
  | not contain any logic for your plugin what so ever.
  |
  */


require_once('vendor/autoload.php');
require_once('App/Config/App.php');
require_once $leadpagesConfig['basePath'] . 'Framework/ServiceContainer/ServiceContainer.php';
require_once($leadpagesConfig['basePath'].'App/Config/RegisterProviders.php');


/*
  |--------------------------------------------------------------------------
  | Admin Bootstrap
  |--------------------------------------------------------------------------
  |
  |
  |
  */

if (is_admin() || is_network_admin()) {
    $adminBootstrap = $app['adminBootstrap'];
    //include('App/Helpers/ErrorHandlerAjax.php');
}
