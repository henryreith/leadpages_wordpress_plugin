<?php

$leadpagesConfig = array(

    /*
  |--------------------------------------------------------------------------
  | Base Path
  |--------------------------------------------------------------------------
  |
  | Base path setup for a wordpress plugin
  |
  */
  'basePath' => plugin_dir_path(dirname(dirname(__FILE__))),
  'api'      => array(
    'sessions'  => array(
      'new'     => 'https://api.leadpages.io/auth/v1/sessions/',
      'current' => 'https://api.leadpages.io/auth/v1/sessions/current'
    ),
    'pages'     => 'https://my.leadpages.net/page/v1/pages',
    'leadboxes' => 'https://my.leadpages.net/leadbox/v1/leadboxes'
  )
);


$leadpagesConfig['update_url'] = "http://leadbrite.appspot.com";
$leadpagesConfig['admin_assets'] = plugin_dir_url((dirname(__FILE__))) . 'assets';
$leadpagesConfig['admin_images'] = $leadpagesConfig['admin_assets'] . '/images';

