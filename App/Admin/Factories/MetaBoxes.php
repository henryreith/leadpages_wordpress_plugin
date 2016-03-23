<?php

namespace Leadpages\Admin\Factories;

use TheLoop\Contracts\Factory;
use TheLoop\Contracts\RegisterDependencies;

class Metaboxes implements Factory
{

    public static function create($metaBox, $dependencies = array())
    {
        $metaBox = new $metaBox();
        if($metaBox instanceof RegisterDependencies){
            $metaBox->register($dependencies);
        }
        $metaBox->registerMetaBox();
    }
}