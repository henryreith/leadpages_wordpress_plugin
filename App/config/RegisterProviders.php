<?php

use Leadpages\Front\Providers\NF;
use Leadpages\Helpers\Security;
use Leadpages\Bootstrap\AdminBootstrap;
use Leadpages\Bootstrap\FrontBootstrap;
use Leadpages\Admin\Providers\AdminAuth;
use Leadpages\Front\Providers\WelcomeGate;
use TheLoop\Providers\WordPressHttpClient;
use TheLoop\ServiceContainer\ServiceContainer;
use Leadpages\Admin\Providers\LeadpagesLoginApi;
use Leadpages\Admin\Providers\LeadpagesPagesApi;
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
 */

$ioc['pagesApi'] = function($c){
    return new LeadpagesPagesApi($c['httpClient']);
};

$ioc['leadpagesPostType'] = function($c){
  return new LeadpagesPostType();
};


$ioc['welcomeGate'] = function($c){
    return new WelcomeGate();
};

$ioc['nfPage'] = function($c){
    return new NF();
};

/**
 * Front Bootstrap
 */
$ioc['frontBootStrap'] = $ioc->factory(function($c){
    return new FrontBootstrap($c['pagesApi'], $c['leadpagesPostType']);
});

/**
 * Admin Bootstrap
 */
$ioc['adminBootStrap'] = $ioc->factory(function($c){
    return new AdminBootstrap($c['httpClient'], $c['loginApi'], $c['adminAuth']);
});