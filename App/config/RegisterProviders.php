<?php

use App\Providers\WordPressHttpClient;
use TheLoop\ServiceContainer\ServiceContainer;

$container = new ServiceContainer();
$ioc = $container->getContainer();

/**
 * HTTP CLIENT
 */
$ioc['httpClient'] = $ioc->factory(function ($c) {
    return new WordPressHttpClient();
});