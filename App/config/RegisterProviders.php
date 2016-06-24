<?php

use GuzzleHttp\Client;
use Leadpages\Pages\LeadpagesPages;
use LeadpagesWP\Helpers\PasswordProtected;
use LeadpagesWP\Lib\ApiResponseHandler;
use LeadpagesWP\Bootstrap\AdminBootstrap;
use LeadpagesWP\Bootstrap\FrontBootstrap;
use LeadpagesWP\models\LeadPagesPostTypeModel;
use TheLoop\ServiceContainer\ServiceContainer;
use LeadpagesWP\Front\Controllers\NotFoundController;
use LeadpagesWP\Front\Controllers\LeadpageController;
use LeadpagesWP\ServiceProviders\WordPressLeadpagesAuth;
use LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType;
use LeadpagesWP\Front\Controllers\WelcomeGateController;

/*
|--------------------------------------------------------------------------
| Instantiate Service Container
|--------------------------------------------------------------------------
|
|
*/

$leadpagesContainer = new ServiceContainer();
$leadpagesApp       = $leadpagesContainer->getContainer();

/**
 * register config into container
 */
$leadpagesApp['config'] = $leadpagesConfig;

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
 * @param $leadpagesApp
 *
 * @return \TheLoop\Providers\WordPressHttpClient
 */
$leadpagesApp['httpClient'] = function ($leadpagesApp) {
    return new Client();
};

$leadpagesApp['adminBootstrap'] = function($leadpagesApp){
    return new AdminBootstrap($leadpagesApp['leadpagesLogin'], $leadpagesApp['lpPostTypeModel']);
};

$leadpagesApp['frontBootstrap'] = function($leadpagesApp){
    return new FrontBootstrap($leadpagesApp['leadpagesLogin'], $leadpagesApp['leadpageController'], $leadpagesApp['pagesApi']);
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
 * @param $leadpagesApp
 *
 * @return \LeadpagesWP\Lib\ApiResponseHandler
 */
$leadpagesApp['apiResponseHandler'] = function($leadpagesApp){
    return new ApiResponseHandler();
};

/**
 * Leadpages login api object
 * @param $leadpagesApp
 *
 * @return \LeadpagesWP\ServiceProviders\LeadpagesLogin
 */
$leadpagesApp['leadpagesLogin'] = function($leadpagesApp){
  return new WordPressLeadpagesAuth($leadpagesApp['httpClient']);
};

$leadpagesApp['pagesApi'] = function($leadpagesApp){
    return new LeadpagesPages($leadpagesApp['httpClient'], $leadpagesApp['leadpagesLogin']);
};

$leadpagesApp['lpPostType'] = function($leadpagesApp){
    return new LeadpagesPostType();
};


$leadpagesApp['lpPostTypeModel'] = function($leadpagesApp){
  return new LeadPagesPostTypeModel($leadpagesApp['pagesApi'], $leadpagesApp['lpPostType']);
};


$leadpagesApp['passwordProtected'] = function ($leadpagesApp) {
    global $wpdb;
    return new PasswordProtected($wpdb);
};


$leadpagesApp['leadpageController'] = function($leadpagesApp){
    return new LeadpageController($leadpagesApp['notfound'], $leadpagesApp['WelcomeGateController'], $leadpagesApp['lpPostTypeModel'], $leadpagesApp['pagesApi'], $leadpagesApp['passwordProtected']);
};
$leadpagesApp['notfound'] = function($leadpagesApp){
    return new NotFoundController($leadpagesApp['lpPostTypeModel'], $leadpagesApp['pagesApi']);
};

$leadpagesApp['WelcomeGateController'] = function($leadpagesApp){
    return new WelcomeGateController();
};

