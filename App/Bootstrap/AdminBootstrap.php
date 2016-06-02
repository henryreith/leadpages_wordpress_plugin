<?php

namespace Leadpages\Bootstrap;

use TheLoop\Contracts\HttpClient;
use Leadpages\models\LeadboxesModel;
use Leadpages\Admin\Providers\Update;
use Leadpages\Admin\Factories\Metaboxes;
use Leadpages\Admin\Providers\AdminAuth;
use Leadpages\Admin\Providers\LeadboxApi;
use Leadpages\Admin\Factories\SettingsPage;
use Leadpages\models\LeadPagesPostTypeModel;
use Leadpages\Admin\SettingsPages\Leadboxes;
use Leadpages\Admin\MetaBoxes\LeadboxMetaBox;
use Leadpages\Admin\MetaBoxes\LeadpageSelect;
use Leadpages\Admin\Factories\CustomPostType;
use Leadpages\Admin\Providers\LeadpagesLoginApi;
use Leadpages\Admin\MetaBoxes\LeadpageTypeMetaBox;
use TheLoop\ServiceContainer\ServiceContainerTrait;
use Leadpages\Admin\SettingsPages\LeadpagesLoginPage;
use Leadpages\Admin\CustomPostTypes\LeadpagesPostType;

class AdminBootstrap
{
    use ServiceContainerTrait;
    /**
     * @var \TheLoop\Contracts\HttpClient
     */
    private $httpClient;
    /**
     * @var \Leadpages\Admin\Providers\LeadpagesLoginApi
     */
    private $leadpagesLoginApi;
    /**
     * @var \Leadpages\Admin\Providers\AdminAuth
     */
    private $auth;

    private $ioc;
    /**
     * @var \Leadpages\Admin\Providers\Update
     */
    private $update;
    /**
     * @var \Leadpages\models\LeadPagesPostTypeModel
     */
    private $leadpagesModel;
    /**
     * @var \Leadpages\Admin\Providers\LeadboxApi
     */
    private $leadboxApi;

    public function __construct(HttpClient $httpClient, LeadpagesLoginApi $leadpagesLoginApi, AdminAuth $auth, Update $update, LeadPagesPostTypeModel $leadpagesModel, LeadboxApi $leadboxApi)
    {
        $this->httpClient = $httpClient;
        $this->leadpagesLoginApi = $leadpagesLoginApi;
        $this->auth = $auth;
        $this->leadpagesModel = $leadpagesModel;
        $this->update = $update;
        $this->leadboxApi = $leadboxApi;
        $this->initAdmin();
    }

    public function initAdmin()
    {
        add_action( 'admin_enqueue_scripts', array($this, 'loadJS') );

        $this->auth->login();

        if(!$this->auth->isLoggedIn()){
            SettingsPage::create(LeadpagesLoginPage::getName());
        }else{
            $this->registerRequiredItems();
            LeadboxesModel::init();
            $this->saveLeadPage();
            $this->saveLeadboxes();
            $this->update->silent_update_check();
        }


    }
    public function registerRequiredItems(){
        CustomPostType::create(LeadpagesPostType::getName());
        Metaboxes::create(LeadpageTypeMetaBox::getName());
        Metaboxes::create(LeadpageSelect::getName());
        Metaboxes::create(LeadboxMetaBox::getName());
        SettingsPage::create(Leadboxes::getName());
        add_action( 'wp_ajax_nopriv_getLeadboxesAjax', array($this->leadboxApi, 'allLeadboxesAjax') );
        add_action( 'wp_ajax_getLeadboxesAjax', array($this->leadboxApi, 'allLeadboxesAjax') );


    }
    protected function saveLeadPage(){
        $this->leadpagesModel->save();
    }
    protected function saveLeadboxes()
    {
        LeadboxesModel::saveLeadboxMeta();
    }
    public function loadJS(){
        global $leadpagesConfig;
        wp_enqueue_style( 'leadpagesStyles', $leadpagesConfig['admin_assets'].'/css/styles.css', false );
        if($leadpagesConfig['currentScreen'] == 'leadpages_post') {
            wp_enqueue_script('LeadpagesPostType', $leadpagesConfig['admin_assets'] . '/js/LeadpagesPostType.js',
              array('jquery'));
        }
        wp_localize_script( 'LeadpagesPostType', 'ajax_object', array(
          'ajax_url' => admin_url( 'admin-ajax.php' ),
          'id'       => get_the_ID()
        ));
    }
}