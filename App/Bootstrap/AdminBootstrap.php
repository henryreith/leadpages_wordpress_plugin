<?php

namespace LeadpagesWP\Bootstrap;

use LeadpagesWP\Admin\Factories\MetaBoxes;
use LeadpagesWP\Admin\Factories\SettingsPage;
use LeadpagesWP\Admin\MetaBoxes\LeadpageSlug;
use LeadpagesWP\Admin\MetaBoxes\LeadpageType;
use LeadpagesWP\models\LeadPagesPostTypeModel;
use LeadpagesWP\Admin\Factories\CustomPostType;
use LeadpagesWP\Admin\MetaBoxes\LeadpageSelect;
use LeadpagesWP\Admin\SettingsPages\LeadpagesLoginPage;
use LeadpagesWP\ServiceProviders\WordPressLeadpagesAuth;
use LeadpagesWP\Admin\CustomPostTypes\LeadpagesPostType;

class AdminBootstrap
{

    /**
     * @var \LeadpagesWP\ServiceProviders\LeadpagesLogin
     */
    private $login;
    public $isLoggedIn;
    /**
     * @var \LeadpagesWP\models\LeadPagesPostTypeModel
     */
    private $postTypeModel;

    public function __construct(WordPressLeadpagesAuth $login, LeadPagesPostTypeModel $postTypeModel)
    {
        $this->login = $login;
        $this->postTypeModel = $postTypeModel;

        $this->setupLogin();
        $this->setupLeadpages();
    }

    public function setupLogin()
    {
        if(!$this->login->isLoggedIn())
        {
            //create login form page if user is not logged in
            SettingsPage::create(LeadpagesLoginPage::getName());
            //register hook to listen for admin post of login form
            $this->login->loginHook();
        }else {
            //$this->login->setLoggedInCookie();
            $this->isLoggedIn = true;
            $this->login->getToken();
        }
    }

    public function setupLeadpages()
    {
        //dont execute if not logged in
        if(!$this->isLoggedIn) return;

        CustomPostType::create(LeadpagesPostType::getName());
        MetaBoxes::create(LeadpageSlug::getName());
        MetaBoxes::create(LeadpageType::getName());
        Metaboxes::create(LeadpageSelect::getName());
        add_action('admin_enqueue_scripts', array($this, 'loadJS'));
        add_action('admin_enqueue_scripts', array($this, 'loadStyles'));
        //setup hook for saving Leadpages Post Type
        $this->postTypeModel->save();


    }

    public function setupLeadboxes()
    {
        //dont execute if not logged in
        if(!$this->isLoggedIn) return;

    }


    public function loadJS(){
        global $leadpagesConfig;
        if($leadpagesConfig['currentScreen'] == 'leadpages_post') {
            wp_enqueue_script('LeadpagesPostType', $leadpagesConfig['admin_assets'] . '/js/LeadpagesPostType.js',
              array('jquery'));
        }
        wp_localize_script( 'LeadpagesPostType', 'ajax_object', array(
          'ajax_url' => admin_url( 'admin-ajax.php' ),
          'id'       => get_the_ID(),
        ));
    }

    public function loadStyles(){
        global $leadpagesConfig;
        if($leadpagesConfig['currentScreen'] == 'leadpages_post') {
            wp_enqueue_style('lp-lego', 'https://static.leadpages.net/lego/1.0.30/lego.min.css');
            wp_enqueue_style('lp-styles', $leadpagesConfig['admin_css'] . 'styles.css');
        }
    }
}