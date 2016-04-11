<?php

namespace Leadpages\Bootstrap;

use Leadpages\models\LeadPagesPostTypeModel;
use TheLoop\Contracts\HttpClient;
use Leadpages\Admin\Factories\Metaboxes;
use Leadpages\Admin\Providers\AdminAuth;
use Leadpages\Admin\Factories\SettingsPage;
use Leadpages\admin\MetaBoxes\LeadpageSelect;
use Leadpages\Admin\Factories\CustomPostType;
use Leadpages\Admin\Providers\LeadpagesLoginApi;
use Leadpages\admin\MetaBoxes\LeadpageTypeMetaBox;
use TheLoop\ServiceContainer\ServiceContainerTrait;
use Leadpages\admin\SettingsPages\LeadpagesLoginPage;
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
        /**
         * set http time out to 20 seconds as sometimes our request take
         * a couple seconds longer than the default 5 seconds
         */
        apply_filters('http_request_timeout', 20);
        add_action( 'admin_enqueue_scripts', array($this, 'loadJS') );

        $this->auth->login();

        if(!$this->auth->isLoggedIn()){
            SettingsPage::create(LeadpagesLoginPage::class);
        }else{
            CustomPostType::create(LeadpagesPostType::class);
            Metaboxes::create(LeadpageTypeMetaBox::class);
            Metaboxes::create(LeadpageSelect::class);
            $this->saveLeadPage();
        }




    }

    protected function saveLeadPage(){
        $LeadpagesModel = $this->ioc['leadpagesModel'];
        $LeadpagesModel->save();
    }

    public function loadJS(){
        global $config;
        wp_enqueue_script('LeadpagesPostType', $config['admin_assets'].'/js/LeadpagesPostType.js', array('jquery'));
        wp_localize_script( 'LeadpagesPostType', 'ajax_object', array(
          'ajax_url' => admin_url( 'admin-ajax.php' ),
          'id'       => get_the_ID()
        ));
    }
}