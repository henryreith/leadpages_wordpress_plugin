<?php

use Leadpages\Helpers\Security;
use Leadpages\Front\Providers\NF;
use Leadpages\Admin\Providers\Update;
use Leadpages\Bootstrap\AdminBootstrap;
use Leadpages\Bootstrap\FrontBootstrap;
use Leadpages\Admin\Providers\AdminAuth;
use Leadpages\Admin\Providers\LeadboxApi;
use Leadpages\Front\Providers\WelcomeGate;
use TheLoop\Providers\WordPressHttpClient;
use Leadpages\models\LeadPagesPostTypeModel;
use TheLoop\ServiceContainer\ServiceContainer;
use Leadpages\Admin\Providers\LeadpagesLoginApi;
use Leadpages\Admin\Providers\LeadpagesPagesApi;
use Leadpages\Front\Providers\PasswordProtected;
use Leadpages\Admin\CustomPostTypes\LeadpagesPostType;


$container = new ServiceContainer();
$ioc = $container->getContainer();

/**
 * HTTP CLIENT
 */
$ioc['httpClient'] = $ioc->factory(function ($c) {
    return new WordPressHttpClient();
});


$ioc['security'] = function($c){
    return new Security();
};

/**
 * Login API
 */

$ioc['loginApi'] = $ioc->factory(function($c){
   return new LeadpagesLoginApi($c['httpClient']);
});

/**
 *
 * @param $c
 *
 * @return \Leadpages\Admin\Providers\AdminAuth
 */
$ioc['adminAuth'] = function ($c) {
    return new AdminAuth($c['loginApi'], $c['security']);
};

/**
 * pagesApi
 *
 * @param $c
 *
 * @return \Leadpages\Admin\Providers\LeadpagesPagesApi
 */

$ioc['pagesApi'] = function($c){
    return new LeadpagesPagesApi($c['httpClient']);
};

/**
 * @param $c
 *
 * @return \Leadpages\Helpers\Security
 */
$ioc['security'] = function($c){
  return new Security();
};

/**
 * @param $c
 *
 * @return \Leadpages\Admin\Providers\LeadboxApi
 */
$ioc['leadboxApi'] = function($c){
  return new LeadboxApi($c['httpClient']);
};

/**
 * @param $c
 *
 * @return \Leadpages\Admin\CustomPostTypes\LeadpagesPostType
 */
$ioc['leadpagesPostType'] = function($c){
  return new LeadpagesPostType();
};


/**
 * @param $c
 *
 * @return \Leadpages\models\LeadPagesPostTypeModel
 */
$ioc['leadpagesModel'] = function($c){
    return new LeadPagesPostTypeModel($c['pagesApi'], $c['leadpagesPostType']);
};

/**
 * @param $c
 *
 * @return \Leadpages\Front\Providers\WelcomeGate
 */
$ioc['welcomeGate'] = function($c){
    return new WelcomeGate();
};

/**
 * @param $c
 *
 * @return \Leadpages\Front\Providers\NF
 */
$ioc['nfPage'] = function($c){
    return new NF();
};


/**
 * @param $c
 *
 * @return \Leadpages\Front\Providers\PasswordProtected
 */
$ioc['passwordProtected'] = function($c){
  global $wpdb;
    return new PasswordProtected($wpdb);
};

/**
 * Front Bootstrap
 */
$ioc['frontBootStrap'] = $ioc->factory(function($c){
    return new FrontBootstrap($c['pagesApi'], $c['leadpagesPostType'], $c['leadboxApi']);
});


/**
 * Update Provider
 */

$ioc['update'] = function($c){
    return new Update();
};

/**
 * Admin Bootstrap
 */
$ioc['adminBootStrap'] = $ioc->factory(function($c){
    return new AdminBootstrap($c['httpClient'], $c['loginApi'], $c['adminAuth'], $c['update']);
});

