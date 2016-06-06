<?php

class Test_WP_Simple_Plugin extends WP_UnitTestCase {

    public function test_constants () {
        $plugins = get_plugins();
        print_r($plugins);
    }
}