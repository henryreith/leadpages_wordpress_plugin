<?php

namespace LeadpagesWP\Front\Controllers;

use LeadpagesWP\models\LeadboxesModel;
use LeadpagesWP\ServiceProviders\LeadboxesApi;

class LeadboxController
{

    protected $postType;
    /**
     * @var
     */
    private $leadboxApi;

    private $hasSpecificTimed;
    private $hasSpecificExit;
    private $pageSpecificTimedLeadboxId;
    private $pageSpecificExitdLeadboxId;


    public function __construct(LeadboxesApi $leadboxApi)
    {
        $this->leadboxApi = $leadboxApi;
    }

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

    protected function setPageType($post)
    {
        $this->postType = $post->post_type;
    }

    protected function getGlobalLeadBoxes(){

        $currentTimedLeadbox = LeadboxesModel::getCurrentTimedLeadbox();
        $currentExitLeadbox  = LeadboxesModel::getCurrentExitLeadbox();
        return array(
          'timed' => $currentTimedLeadbox,
          'exit'  => $currentExitLeadbox
        );
    }

    public function getTimedLeadboxCode($leadboxes){
        $leadboxes = $this->getGlobalLeadBoxes();
        if($leadboxes['timed'][1] == $this->postType || $leadboxes['timed'][1] == 'all'){
            $apiResponse = $this->leadboxApi->getSingleLeadboxEmbedCode($leadboxes['timed'][0], 'timed');
            $timed_embed_code = json_decode($apiResponse['response'], true);
        }
        if(empty($timed_embed_code)){
            return;
        }
        return $timed_embed_code['embed_code'];
    }

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

    public function addTimedLeadboxesGlobal($content){
        $leadboxes = $this->getGlobalLeadBoxes();
        $content = $content . $this->getTimedLeadboxCode($leadboxes);
        return $content;
    }

    public function addExitLeadboxesGlobal($content){
        $leadboxes = $this->getGlobalLeadBoxes();
        $content   = $content . $this->getExitLeadboxCode($leadboxes);
        return $content;
    }

    public function addEmbedToContent(){
        if($this->hasSpecificTimed){
            add_filter('the_content', array($this, 'displayPageSpecificTimedLeadbox'));
            $this->woocommerce_specific_hook('displayPageSpecificTimedLeadbox');
        }else {
            add_filter('the_content', array($this, 'addTimedLeadboxesGlobal'));
            $this->woocommerce_specific_hook('addTimedLeadboxesGlobal');
        }

        if($this->hasSpecificExit) {
            add_filter('the_content', array($this, 'displayPageSpecificExitLeadbox'));
            $this->woocommerce_specific_hook('displayPageSpecificExitLeadbox');
        }else{
            add_filter('the_content', array($this, 'addExitLeadboxesGlobal'));
            $this->woocommerce_specific_hook('addExitLeadboxesGlobal');
        }
    }

    protected function woocommerce_specific_hook($method){
        if($this->postType == 'product'){
            add_action('woocommerce_after_main_content', array($this, $method));
        }
    }

    /*
     * Page Specific Leadboxes
     */

    protected function getPageSpecificTimedLeadbox($post){
        if(!is_front_page()) {
            $this->pageSpecificTimedLeadboxId = get_post_meta($post->ID, 'pageTimedLeadbox', true);
            if (!empty($this->pageSpecificTimedLeadboxId)) {
                $this->hasSpecificTimed = true;
            }
        }
    }

    public function displayPageSpecificTimedLeadbox($content){
        //only display a leadbox if the id selected is not none.
        //if none is selected nothing will show.
        if($this->pageSpecificTimedLeadboxId != 'none') {
            $apiResponse = $this->leadboxApi->getSingleLeadboxEmbedCode($this->pageSpecificTimedLeadboxId, 'timed');
            $timed_embed_code = json_decode($apiResponse['response'], true);
        }
        if(isset($timed_embed_code['embed_code'])) {
            return $content . $timed_embed_code['embed_code'];
        }else{
            return $content;
        }

    }

    protected function getExitSpecifiExitLeadbox($post){
        if(!is_front_page()) {
            $this->pageSpecificExitdLeadboxId = get_post_meta($post->ID, 'pageExitLeadbox', true);
            if (!empty($this->pageSpecificExitdLeadboxId)) {
                $this->hasSpecificExit = true;
            }
        }
    }

    public function displayPageSpecificExitLeadbox($content){
        //only display a leadbox if the id selected is not none.
        //if none is selected nothing will show.
        if($this->pageSpecificExitdLeadboxId != 'none') {
            $apiResponse = $this->leadboxApi->getSingleLeadboxEmbedCode($this->pageSpecificExitdLeadboxId, 'exit');
            $exit_embed_code = json_decode($apiResponse['response'], true);
        }
        if(isset($exit_embed_code['embed_code'])) {
            return $content . $exit_embed_code['embed_code'];
        }else{
            return $content;
        }
    }

}