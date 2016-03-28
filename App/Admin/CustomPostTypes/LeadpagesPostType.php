<?php


namespace Leadpages\Admin\CustomPostTypes;

use TheLoop\Contracts\CustomPostType;
use TheLoop\Contracts\CustomPostTypeColumns;

class LeadpagesPostType extends CustomPostType implements CustomPostTypeColumns
{

    private $labels = array();
    private $args   = array();
    public $postTypeName = 'leadpages_post';

    public function defineLabels()
    {
        $this->labels = array(
          'name'               => _x('LeadPages', 'post type general name'),
          'singular_name'      => _x('LeadPage', 'post type singular name'),
          'add_new'            => _x('Add New', 'leadpage'),
          'add_new_item'       => __('Add New LeadPage'),
          'edit_item'          => __('Edit LeadPage'),
          'new_item'           => __('New LeadPage'),
          'view_item'          => __('View LeadPages'),
          'search_items'       => __('Search LeadPages'),
          'not_found'          => __('Nothing found'),
          'not_found_in_trash' => __('Nothing found in Trash'),
          'parent_item_colon'  => ''
        );
    }

    public function registerPostType()
    {
        global $config;
        $this->args   = array(
          'labels'               => $this->labels,
          'description'          => 'Allows you to have LeadPages on your WordPress site.',
          'public'               => true,
          'publicly_queryable'   => true,
          'show_ui'              => true,
          'query_var'            => true,
          'menu_icon'            => $config['admin_images'].'/menu-icon.png',
          'capability_type'      => 'page',
          'menu_position'        => null,
          'rewrite'              => array(
            'with_front' => false,
            'slug'       => '/'
          ),
          'can_export'           => false,
          'hierarchical'         => true,
          'has_archive'          => true,
          'supports'             => array(),
        );

        register_post_type( $this->postTypeName, $this->args );
        remove_post_type_support($this->postTypeName, 'editor');
        //remove_post_type_support($this->postTypeName, 'title');

    }


    public function defineColumns($columns)
    {
        $cols                        = array();
        $cols['cb']                  = $columns['cb'];
        $cols[$this->postTypeName.'_name'] = __('Name', 'leadpages');
        $cols[$this->postTypeName.'_type'] = __('Type', 'leadpages');
        $cols[$this->postTypeName.'_path'] = __('Url', 'leadpages');
        $cols['date']                      = __('Date', 'leadpages');
        return $cols;;
    }

    public function populateColumns($column)
    {

        $this->populateNameColumn($column);

    }

    private function populateNameColumn($column){
        if ( $this->postTypeName.'_name' == $column ) {
            $url    = get_edit_post_link( get_the_ID() );
            $p_name = get_post_meta( get_the_ID(), 'leadpages_name', true );
            if($p_name == ''){
                $p_name = get_the_title(get_the_ID());
            }
            echo '<strong><a href="' . $url . '">' . $p_name . '</a></strong>';
        }
    }

    /*private function populatePathColumn(){
        if ( 'leadpages_post_path' == $column ) {
            if ( $is_front_page ) {
                $url = site_url() . '/';
                echo '<a href="' . $url . '" target="_blank">' . $url . '</a>';
            } elseif ( $is_nf_page ) {
                $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randomString = '';
                $length       = 10;
                for ( $i = 0; $i < $length; $i ++ ) {
                    $randomString .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
                }
                $url = site_url() . '/random-test-url-' . $randomString;
                echo '<a href="' . $url . '" target="_blank">' . $url . '</a>';
            } else {
                if ( $path == '' ) {
                    echo '<strong style="color:#ff3300">Missing path!</strong> <i>Page is not active</i>';
                } else {
                    $url = site_url() . '/' . $path;
                    echo '<a href="' . $url . '" target="_blank">' . $url . '</a>';
                }
            }
        }
    }*/



    public function addColumns()
    {
        add_filter( 'manage_edit-'.$this->postTypeName.'_columns', array( &$this, 'defineColumns' ) );
        add_action( 'manage_posts_custom_column', array( &$this, 'populateColumns' ) );

    }

    public function buildPostType()
    {
        $this->defineLabels();
        add_action('init', array($this, 'registerPostType'));
        $this->addColumns();
    }
}
