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

        $currentTimedLeadbox = LeadboxesModel::getCurrentTimedLeadbox();
        $currentExitLeadbox  = LeadboxesModel::getCurrentExitLeadbox();
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
     * @param $content
     *
     * @return string
     */
    public function addTimedLeadboxesGlobal($content){
        $leadboxes = $this->getGlobalLeadBoxes();
        $content = $content . $this->getTimedLeadboxCode($leadboxes);
        return $content;
    }

    /**
     * @param $content
     *
     * @return string
     */
    public function addExitLeadboxesGlobal($content){
        $leadboxes = $this->getGlobalLeadBoxes();
        $content   = $content . $this->getExitLeadboxCode($leadboxes);
        return $content;
    }

    /**
     *
     */
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