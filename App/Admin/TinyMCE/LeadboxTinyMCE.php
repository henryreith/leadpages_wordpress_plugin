<?php

namespace LeadpagesWP\Admin\TinyMCE;


class LeadboxTinyMCE
{

    public function init()
    {
        //tinymce setup
        add_action('init', array($this, 'leadboxes_buttons'));
//        foreach (array('post.php', 'post-new.php') as $hook) {
//            add_action("admin_head-$hook", array($this, 'leadboxtiny_mce_vars'));
//        }
    }

    /**
     * register tiny mce button
     *
     * @param $buttons
     *
     * @return mixed
     */

    public function leadboxes_buttons()
    {
        add_filter("mce_external_plugins", array($this, "leadboxes_add_buttons"));
        add_filter('mce_buttons', array($this, 'leadboxes_register_buttons'));
    }

    public function leadboxes_add_buttons($plugin_array)
    {
        global $leadpagesConfig;
        $plugin_array['leadpages_leadboxes'] = $leadpagesConfig['admin_js'] . 'leadbox_tinymce.js';
        return $plugin_array;
    }

    public function leadboxes_register_buttons($buttons)
    {
        array_push($buttons, 'add_leadbox');
        return $buttons;
    }

    public function tinymceVars()
    {
        
    }

}