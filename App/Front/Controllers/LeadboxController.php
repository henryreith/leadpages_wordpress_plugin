<?php

namespace LeadpagesWP\Front\Controllers;

use LeadpagesWP\models\LeadboxesModel;
use LeadpagesWP\ServiceProviders\LeadboxesApi;

/**
 * Class LeadboxController
 * @package LeadpagesWP\Front\Controllers
 */
class LeadboxController
{

    /**
     * @var
     */
    protected $postType;
    /**
     * @var
     */
    private $leadboxApi;

    /**
     * @var
     */
    private $hasSpecificTimed;
    /**
     * @var
     */
    private $hasSpecificExit;
    /**
     * @var
     */
    private $pageSpecificTimedLeadboxId;
    /**
     * @var
     */
    private $pageSpecificExitdLeadboxId;


    /**
     * LeadboxController constructor.
     *
     * @param \LeadpagesWP\ServiceProviders\LeadboxesApi $leadboxApi
     */
    public function __construct(LeadboxesApi $leadboxApi)
    {
        $this->leadboxApi = $leadboxApi;
        $this->globalLeadboxes = $this->getGlobalLeadBoxes();
    }

    /**
     *
     */
    public function initLeadboxes(){
        global $post;

        if(empty($post)){
            return;
        }

        $this->setPageType($post);
        $this->getPageSpecificTimedLeadbox($post);
        $this->getExitSpecifiExitLeadbox($post);
        $this->addEmbedToContent();
    }


    public function initLeadboxes404(){
        $this->addEmbedToContent();
    }

    /**
     * @param $post
     */
    protected function setPageType($post)
    {
        $this->postType = $post->post_type;
    }

    /**
     * @return array
     */
    protected function getGlobalLeadBoxes(){
        $leadboxes = LeadboxesModel::getLpSettings();
        $currentTimedLeadbox = LeadboxesModel::getCurrentTimedLeadbox($leadboxes);
        $currentExitLeadbox  = LeadboxesModel::getCurrentExitLeadbox($leadboxes);
        return array(
          'timed' => $currentTimedLeadbox,
          'exit'  => $currentExitLeadbox
        );
    }

    /**
     * @param $leadboxes
     */
    public function getTimedLeadboxCode($leadboxes){
        if($leadboxes['timed'][1] == $this->postType || $leadboxes['timed'][1] == 'all'){
            $apiResponse = $this->leadboxApi->getSingleLeadboxEmbedCode($leadboxes['timed'][0], 'timed');
            $timed_embed_code = json_decode($apiResponse['response'], true);
        }
        if(empty($timed_embed_code)){
            return;
        }
        return $timed_embed_code['embed_code'];
    }

    /**
     * return js code directly from database if it is there, if not make a call for it
     * hopefully improve page load speeds
     * @param $leadboxes
     */
    public function getGlobalTimedLeadboxJs($leadboxes)
    {
        if(isset($this->globalLeadboxes['timed'][2])) {
            $globalTimedLeadboxJs = $this->globalLeadboxes['timed'][2];
        }
        if(empty($globalTimedLeadboxJs)){
            //include for backward compatibility
            return $this->getTimedLeadboxCode($leadboxes);
        }
        return $globalTimedLeadboxJs;
    }

    /**
     * @param $leadboxes
     */
    public function getExitLeadboxCode($leadboxes){


        if($leadboxes['exit'][1] == $this->postType || $leadboxes['exit'][1] == 'all'){
            $apiResponse = $this->leadboxApi->getSingleLeadboxEmbedCode($leadboxes['exit'][0], 'exit');
            $exit_embed_code = json_decode($apiResponse['response'], true);
        }
        if(empty($exit_embed_code)){
            return;
        }
        return $exit_embed_code['embed_code'];
    }


    /**
     * return js code directly from database if it is there, if not make a call for it
     * hopefully improve page load speeds
     * @param $leadboxes
     */
    public function getGlobalExitLeadboxJs($leadboxes)
    {
        if(isset($this->globalLeadboxes['exit'][2])) {
            $globalExitLeadboxJs = $this->globalLeadboxes['exit'][2];
        }
        if(empty($globalExitLeadboxJs)){
            //include for backward compatibility
            return $this->getExitLeadboxCode($leadboxes);
        }
        return $globalExitLeadboxJs;
    }

    /**
     * @param $content
     *
     * @return string
     */
    public function addTimedLeadboxesGlobal(){
        return $this->getGlobalTimedLeadboxJs($this->globalLeadboxes);
    }

    /**
     * @param $content
     *
     * @return string
     */
    public function addExitLeadboxesGlobal(){
        return $this->getGlobalExitLeadboxJs($this->globalLeadboxes);

    }

    /**
     * @param $content
     */
    public function addEmbedToContent(){
        $messageTimed = '';
        $messageExit  = '';
        if($this->hasSpecificTimed){
            $messageTimed = $messageTimed . $this->displayPageSpecificTimedLeadbox();
            $messageTimed = $messageTimed . $this->woocommerce_specific_hook('displayPageSpecificTimedLeadbox');
            //add_filter('the_content', array($this, 'displayPageSpecificTimedLeadbox'));
        }else {
            $messageTimed = $messageTimed . $this->addTimedLeadboxesGlobal();
            $messageTimed = $messageTimed . $this->woocommerce_specific_hook('addTimedLeadboxesGlobal');
           // add_filter('the_content', array($this, 'addTimedLeadboxesGlobal'));
        }

        if($this->hasSpecificExit) {
            $messageExit = $messageExit . $this->displayPageSpecificExitLeadbox();
            $messageExit = $messageExit . $this->woocommerce_specific_hook('displayPageSpecificExitLeadbox');
            //add_filter('the_content', array($this, 'displayPageSpecificExitLeadbox'));
        }else{
            $messageExit = $messageExit . $this->addExitLeadboxesGlobal();
            $messageExit = $messageExit . $this->woocommerce_specific_hook('addExitLeadboxesGlobal');
            //add_filter('the_content', array($this, 'addExitLeadboxesGlobal'));
        }
        echo $messageTimed;
        echo $messageExit;
    }

    /**
     * @param $method
     */
    protected function woocommerce_specific_hook($method){
        if($this->postType == 'product'){
            add_action('woocommerce_after_main_content', array($this, $method));
        }
    }

    /*
     * Page Specific Leadboxes
     */

    /**
     * @param $post
     */
    protected function getPageSpecificTimedLeadbox($post){
        if(!is_front_page()) {
            $this->pageSpecificTimedLeadboxId = get_post_meta($post->ID, 'pageTimedLeadbox', true);
            if (!empty($this->pageSpecificTimedLeadboxId)) {
                $this->hasSpecificTimed = true;
            }
        }
    }

    /**
     * @param $content
     *
     * @return string
     */
    public function displayPageSpecificTimedLeadbox(){
        //only display a leadbox if the id selected is not none.
        //if none is selected nothing will show.
        if($this->pageSpecificTimedLeadboxId != 'none') {
            $apiResponse = $this->leadboxApi->getSingleLeadboxEmbedCode($this->pageSpecificTimedLeadboxId, 'timed');
            $timed_embed_code = json_decode($apiResponse['response'], true);
        }
        if(isset($timed_embed_code['embed_code'])) {
            return $timed_embed_code['embed_code'];
        }

    }

    /**
     * @param $post
     */
    protected function getExitSpecifiExitLeadbox($post){
        if(!is_front_page()) {
            $this->pageSpecificExitdLeadboxId = get_post_meta($post->ID, 'pageExitLeadbox', true);
            if (!empty($this->pageSpecificExitdLeadboxId)) {
                $this->hasSpecificExit = true;
            }
        }
    }

    /**
     * @param $content
     *
     * @return string
     */
    public function displayPageSpecificExitLeadbox(){
        //only display a leadbox if the id selected is not none.
        //if none is selected nothing will show.
        if($this->pageSpecificExitdLeadboxId != 'none') {
            $apiResponse = $this->leadboxApi->getSingleLeadboxEmbedCode($this->pageSpecificExitdLeadboxId, 'exit');
            $exit_embed_code = json_decode($apiResponse['response'], true);
        }
        if(isset($exit_embed_code['embed_code'])) {
            return $exit_embed_code['embed_code'];
        }
    }

}