<?php

namespace Leadpages\Admin\Factories;

use TheLoop\Contracts\Factory;

class CustomPostType implements Factory {

    public static function create($postType)
    {
        $postType = new $postType();
        $postType->buildPostType();
    }

}