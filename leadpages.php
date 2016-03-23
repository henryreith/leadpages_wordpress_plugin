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

/**
 * Load plugin textdomain.
 */

load_plugin_textdomain( 'leadpages', false, plugin_basename( dirname( __FILE__ ) ) . '/App/Languages' );

use Leadpages\Bootstrap\AdminBootstrap;
use Leadpages\Admin\Providers\AdminAuth;
use Leadpages\Admin\Providers\LeadpagesLoginApi;

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

require $config['basePath'].'Framework/ServiceContainer/ServiceContainer.php';

/*
  |--------------------------------------------------------------------------
  | Application Bootstrap
  |--------------------------------------------------------------------------
  |
  | Include bootstrap files to setup app
  |
  */


$adminBootstrap = $ioc['adminBootStrap'];
$frontBootstrap = $ioc['frontBootStrap'];
