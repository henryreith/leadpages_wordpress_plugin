<?php


namespace Leadpages\Admin\CustomPostTypes;

use Leadpages\Helpers\LeadpageType;
use TheLoop\Contracts\CustomPostType;
use Leadpages\models\LeadPagesPostTypeModel;
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
          'query_var'            => false,
          'menu_icon'            => $config['admin_images'].'/menu-icon.png',
          'capability_type'      => 'page',
          'menu_position'        => 10000,
          'can_export'           => false,
          'hierarchical'         => true,
          'has_archive'          => true,
          'supports'             => array(),
          'rewrite'         => array(
            'with_front'	=> true,
            'slug'          => ''
          ),
        );

        register_post_type( $this->postTypeName, $this->args );
        remove_post_type_support($this->postTypeName, 'editor');
        //remove_post_type_support($this->postTypeName, 'title');

    }

    /**
     * Remove the slug from published post permalinks. Only affect our custom post type, though.
     */
    public function remove_cpt_slug( $post_link, $post, $leavename ) {

        if ( $this->postTypeName != $post->post_type || 'publish' != $post->post_status ) {
            return $post_link;
        }

        $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

        return $post_link;
    }

    /**
     * Have WordPress match postname to any of our public post types (post, page, race)
     * All of our public post types can have /post-name/ as the slug, so they better be unique across all posts
     * By default, core only accounts for posts and pages where the slug is /post-name/
     */
    public function parse_request_trick( $query ) {

        // Only noop the main query
        if ( ! $query->is_main_query() )
            return;

        // Only noop our very specific rewrite rule match
        if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
            return;
        }

        // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
        if ( ! empty( $query->query['name'] ) ) {
            $query->set( 'post_type', array( 'post', 'page', $this->postTypeName ) );
        }
    }

    public function defineColumns($columns)
    {
        $cols                        = array();
        $cols['cb']                  = $columns['cb'];
        $cols[$this->postTypeName.'_name'] = __('Name', 'leadpages');
        $cols[$this->postTypeName.'_type'] = __('Type', 'leadpages');
        $cols[$this->postTypeName.'_path'] = __('Url', 'leadpages');
        $cols['date']                      = __('Date', 'leadpages');
        return $cols;
    }

    public function populateColumns($column)
    {
        $id = get_the_ID();
        $this->populateNameColumn($column, $id);
        $this->populatePathColumn($column, $id);
        $this->populateTypeColumn($column, $id);

    }

    private function populateNameColumn($column, $id){

        if ( $this->postTypeName.'_name' == $column ) {
            $url    = get_edit_post_link( $id );
            $post_name = get_post_meta( $id, 'leadpages_name', true );
            $name = $post_name;
            if($name == ''){
                $name = get_the_title($id);
            }
            echo '<strong><a href="' . $url . '">' . $name . '</a></strong>';
        }
    }

    private function populatePathColumn($column, $id){
        $path = LeadPagesPostTypeModel::getMetaPagePath($id);
        if ( $this->postTypeName.'_path' == $column ) {

            if ( LeadpageType::is_front_page($id) ) {
                $url = site_url() . '/';
                echo '<a href="' . $url . '" target="_blank">' . $url . '</a>';
            } elseif ( LeadpageType::is_nf_page($id) ) {
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
                    $url = $path;
                    echo '<a href="' . $url . '" target="_blank">' . $url . '</a>';
                }
            }
        }
    }

    private function populateTypeColumn($column, $id){

        if ( $this->postTypeName.'_type' == $column ) {
            $type    = LeadPagesPostTypeModel::getMetaPageType( $id );
            switch($type){
                case 'lp':
                    echo 'Normal';
                    break;
                case 'fp':
                    $activePage = LeadpageType::get_front_lead_page($id);
                    echo 'Homepage';
                    echo ($activePage ==  $id ? '<span style="font-style: italic; color:red;">'.__(' Active', 'leadpages').'</span>' : '');
                    break;
                case 'wg':
                    $activePage = LeadpageType::get_wg_lead_page($id);
                    echo 'Welcome Gate';
                    echo ($activePage ==  $id ? '<span style="font-style: italic; color:red;">'.__(' Active', 'leadpages').'</span>' : '');
                    break;
                case 'nf':
                    $activePage = LeadpageType::get_404_lead_page($id);
                    echo '404 Page';
                    echo ($activePage ==  $id ? '<span style="font-style: italic; color:red;">'.__(' Active', 'leadpages').'</span>' : '');
                    break;
            }
        }
    }

    public function addColumns()
    {
        add_filter( 'manage_edit-'.$this->postTypeName.'_columns', array( &$this, 'defineColumns' ) );
        add_action( 'manage_pages_custom_column', array( $this, 'populateColumns' ) );

    }

    public function checkErrorDisplay(){

        $screen = get_current_screen();
        if($screen->id != 'leadpages_post'){
            return;
        }

        global $post;
        $id = $post->ID;

        $pageType = get_post_meta($id, 'leadpages_post_type', true);

        if(!$pageType){
            return;
        }
        switch($pageType){
            case 'fp':
                $activePage = LeadpageType::get_front_lead_page();
               if($activePage && $activePage !== $id){?>
                   <div class="error">
                       <p><?php echo 'Post #'. $activePage .' is already a Home Page. Please remove that page to create a new one.'; ?></p>
                   </div>
                 <?php
               }
                break;
            case 'wg':
                $activePage = LeadpageType::get_wg_lead_page();
                if($activePage && $activePage !== $id) {
                    ?>
                    <div class="error">
                        <p><?php echo 'Post #' . $activePage . ' is already a Welcome Gate&trade;. Please remove that page to create a new one.'; ?></p>
                    </div>
                    <?php
                }
                break;
            case 'nf':
                $activePage = LeadpageType::get_404_lead_page();
                if($activePage && $activePage !== $id){ ?>
                <div class="error">
                    <p><?php echo 'Post #' . $activePage . ' is already a 404 page. Please remove that page to create a new one.'; ?></p>
                </div>
                <?php
            }
                break;
        }

    }

    public function checkError($pageType, $id){

        switch($pageType){
            case 'fp':
                $activePage = LeadpageType::get_front_lead_page();

                if($activePage && $activePage !== $id){
                    return 'error';
                }
                break;
            case 'wg':
                $activePage = LeadpageType::get_wg_lead_page();
                if($activePage && $activePage !== $id) {
                    return 'error';
                }
                break;
            case 'nf':
                $activePage = LeadpageType::get_404_lead_page();
                if($activePage && $activePage !== $id){
                    return 'error';
                }
                break;
        }

    }

    public function buildPostType()
    {
        $this->defineLabels();
        add_action('init', array($this, 'registerPostType'));
        add_filter( 'post_type_link', array($this,'remove_cpt_slug'), 10, 3 );
        add_action( 'pre_get_posts', array($this, 'parse_request_trick' ));
        add_action( 'admin_notices', array($this, 'checkErrorDisplay') );
        $this->addColumns();
    }
}
