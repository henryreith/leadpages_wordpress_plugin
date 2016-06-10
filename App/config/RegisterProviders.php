<?php

use GuzzleHttp\Client;
use LeadpagesWP\Lib\ApiResponseHandler;
use LeadpagesWP\Bootstrap\AdminBootstrap;
use TheLoop\ServiceContainer\ServiceContainer;
use LeadpagesWP\ServiceProviders\WordPressLeadpagesAuth;

/*
|--------------------------------------------------------------------------
| Instantiate Service Container
|--------------------------------------------------------------------------
|
|
*/

$container = new ServiceContainer();
$app       = $container->getContainer();

/**
 * register config into container
 */
$app['config'] = $leadpagesConfig;

/*
|--------------------------------------------------------------------------
| Base Providers
|--------------------------------------------------------------------------
|
| Leadpages Base Service providers
|
*/

/**
 * HttpClient
 * @param $app
 *
 * @return \TheLoop\Providers\WordPressHttpClient
 */
$app['httpClient'] = function ($app) {
    return new Client();
};

$app['adminBootstrap'] = function($app){
    return new AdminBootstrap($app['leadpagesLogin']);
};


/*
|--------------------------------------------------------------------------
| API Providers
|--------------------------------------------------------------------------
|
| Leadpages API Service providers
|
*/


/**
 * response object for handling leadpages api calls
 * @param $app
 *
 * @return \Leadpages\Lib\ApiResponseHandler
 */
$app['apiResponseHandler'] = function($app){
    return new ApiResponseHandler();
};

/**
 * Leadpages login api object
 * @param $app
 *
 * @return \Leadpages\ServiceProviders\LeadpagesLogin
 */
$app['leadpagesLogin'] = function($app){
  return new WordPressLeadpagesAuth($app['httpClient']);
};


