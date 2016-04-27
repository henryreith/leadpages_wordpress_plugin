<?php

namespace Leadpages\Front\Controllers;

use Leadpages\Admin\Providers\LeadboxApi;
use Leadpages\models\LeadboxesModel;

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


    public function __construct(LeadboxApi $leadboxApi)
    {
        $this->leadboxApi = $leadboxApi;
    }

    public function initLeadboxes(){
        global $post;
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

    public function getTimedLeadboxCode($content){
        $leadboxes = $this->getGlobalLeadBoxes();
        if($leadboxes['timed'][1] == $this->postType || $leadboxes['timed'][1] == 'all'){
            $timed_embed_code = $this->leadboxApi->getSingleLeadbox($leadboxes['timed'][0]);
        }

        return $content . $timed_embed_code;
    }

    public function getExitLeadboxCode($content){
        $leadboxes = $this->getGlobalLeadBoxes();

        if($leadboxes['exit'][1] == $this->postType || $leadboxes['exit'][1] == 'all'){
            $exit_embed_code = $this->leadboxApi->getSingleLeadbox($leadboxes['exit'][0]);
        }
        return $content . $exit_embed_code;

    }

    public function addEmbedToContent(){
        if($this->hasSpecificTimed){
            add_filter('the_content', array($this, 'displayPageSpecificTimedLeadbox'));
            $this->woocommerce_specific_hook('displayPageSpecificTimedLeadbox');
        }else {
            add_filter('the_content', array($this, 'getTimedLeadboxCode'));
            $this->woocommerce_specific_hook('getTimedLeadboxCode');
        }

        if($this->hasSpecificExit) {
            add_filter('the_content', array($this, 'displayPageSpecificExitLeadbox'));
            $this->woocommerce_specific_hook('displayPageSpecificExitLeadbox');
        }else{
            add_filter('the_content', array($this, 'getExitLeadboxCode'));
            $this->woocommerce_specific_hook('getExitLeadboxCode');
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
        $this->pageSpecificTimedLeadboxId = get_post_meta($post->ID, 'pageTimedLeadbox', true);
        if(!empty($this->pageSpecificTimedLeadboxId)){
            $this->hasSpecificTimed = true;
        }
    }

    public function displayPageSpecificTimedLeadbox($content){
        //only display a leadbox if the id selected is not none.
        //if none is selected nothing will show.
        if($this->pageSpecificTimedLeadboxId != 'none') {
            $timed_embed_code = $this->leadboxApi->getSingleLeadbox($this->pageSpecificTimedLeadboxId);
        }
        return $content . $timed_embed_code;
    }

    protected function getExitSpecifiExitLeadbox($post){
        $this->pageSpecificExitdLeadboxId = get_post_meta($post->ID, 'pageExitLeadbox', true);
        if(!empty($this->pageSpecificExitdLeadboxId)){
            $this->hasSpecificExit = true;
        }
    }

    public function displayPageSpecificExitLeadbox($content){
        //only display a leadbox if the id selected is not none.
        //if none is selected nothing will show.
        if($this->pageSpecificExitdLeadboxId != 'none') {
            $exit_embed_code = $this->leadboxApi->getSingleLeadbox($this->pageSpecificExitdLeadboxId);
        }
        return $content . $exit_embed_code;
    }

}