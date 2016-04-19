<?php

namespace Leadpages\Bootstrap;

use Leadpages\Admin\MetaBoxes\LeadboxMetaBox;
use TheLoop\Contracts\HttpClient;
use Leadpages\models\LeadboxesModel;
use Leadpages\Admin\Factories\Metaboxes;
use Leadpages\Admin\Providers\AdminAuth;
use Leadpages\Admin\Factories\SettingsPage;
use Leadpages\Admin\SettingsPages\Leadboxes;
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

    public function __construct(HttpClient $httpClient, LeadpagesLoginApi $leadpagesLoginApi, AdminAuth $auth)
    {

        $this->httpClient = $httpClient;
        $this->leadpagesLoginApi = $leadpagesLoginApi;
        $this->auth = $auth;
        $this->ioc = $this->getContainer();
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
        }




    }
    public function registerRequiredItems(){
        CustomPostType::create(LeadpagesPostType::getName());
        Metaboxes::create(LeadpageTypeMetaBox::getName());
        Metaboxes::create(LeadpageSelect::getName());
        Metaboxes::create(LeadboxMetaBox::getName());

        SettingsPage::create(Leadboxes::getName());
    }
    protected function saveLeadPage(){
        $LeadpagesModel = $this->ioc['leadpagesModel'];
        $LeadpagesModel->save();
    }
    protected function saveLeadboxes()
    {
        LeadboxesModel::saveLeadboxMeta();
    }
    public function loadJS(){
        global $config;
        wp_enqueue_style( 'leadpagesStyles', $config['admin_assets'].'/css/styles.css', false );

        wp_enqueue_script('LeadpagesPostType', $config['admin_assets'].'/js/LeadpagesPostType.js', array('jquery'));
        wp_localize_script( 'LeadpagesPostType', 'ajax_object', array(
          'ajax_url' => admin_url( 'admin-ajax.php' ),
          'id'       => get_the_ID()
        ));
    }
}