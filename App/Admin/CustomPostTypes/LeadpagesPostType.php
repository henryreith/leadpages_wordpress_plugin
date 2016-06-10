<?php


namespace LeadpagesWP\Admin\CustomPostTypes;

use LeadpagesWP\Lib\LeadpageType;
use TheLoop\Contracts\CustomPostType;
use LeadpagesWP\models\LeadPagesPostTypeModel;
use TheLoop\Contracts\CustomPostTypeColumns;

class LeadpagesPostType extends CustomPostType
{

    private $labels = array();
    private $args   = array();
    public $postTypeName = 'leadpages_post';

    public static function getName(){
        return get_called_class();
    }

    public function defineLabels()
    {
        $this->labels = array(
          'name'               => _x('Leadpages', 'post type general name'),
          'singular_name'      => _x('Leadpage', 'post type singular name'),
          'add_new'            => _x('Add New', 'leadpage'),
          'add_new_item'       => __('Add New Leadpage'),
          'edit_item'          => __('Edit Leadpage'),
          'new_item'           => __('New Leadpage'),
          'view_item'          => __('View Leadpages'),
          'search_items'       => __('Search Leadpages'),
          'not_found'          => __('Nothing found'),
          'not_found_in_trash' => __('Nothing found in Trash'),
          'parent_item_colon'  => ''
        );
    }

    public function registerPostType()
    {
        global $leadpagesConfig;
        $this->args   = array(
          'labels'               => $this->labels,
          'description'          => 'Allows you to have Leadpages on your WordPress site.',
          'public'               => true,
          'publicly_queryable'   => true,
          'show_ui'              => true,
          'query_var'            => true,
          'menu_icon'            => $leadpagesConfig['admin_images'].'/menu-icon.png',
          'capability_type'      => 'page',
          'menu_position'        => 10000,
          'can_export'           => false,
          'hierarchical'         => true,
          'has_archive'          => true,
          'supports'             => array(),
        );

        register_post_type( $this->postTypeName, $this->args );
        remove_post_type_support($this->postTypeName, 'editor');

    }

    public function buildPostType()
    {
        $this->defineLabels();
        add_action('init', array($this, 'registerPostType'));
    }
}
