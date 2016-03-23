<?php

namespace Leadpages\Admin\Factories;

use TheLoop\Contracts\Factory;

class SettingsPage implements Factory
{

    public static function create($settingsPage)
    {
        $metaBox = new $settingsPage();
        $metaBox->registerPage();
    }
}