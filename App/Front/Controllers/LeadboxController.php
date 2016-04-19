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

    public function getTimedLeadboxCode(){
        $leadboxes = $this->getGlobalLeadBoxes();
        if($leadboxes['timed'][1] == $this->postType || $leadboxes['timed'][1] == 'all'){

            $timed_embed_code = $this->leadboxApi->getSingleLeadbox($leadboxes['timed'][0]);
            echo $timed_embed_code;
        }
    }

    public function getExitLeadboxCode(){
        $leadboxes = $this->getGlobalLeadBoxes();

        if($leadboxes['exit'][1] == $this->postType || $leadboxes['exit'][1] == 'all'){
            $exit_embed_code = $this->leadboxApi->getSingleLeadbox($leadboxes['exit'][0]);
            echo $exit_embed_code;
        }
    }

    public function addEmbedToContent(){
        if($this->hasSpecificTimed){
            add_filter('the_content', array($this, 'displayPageSpecificTimedLeadbox'));
        }else {
            add_filter('the_content', array($this, 'getTimedLeadboxCode'));
        }

        if($this->hasSpecificExit) {
            add_filter('the_content', array($this, 'displayPageSpecificExitLeadbox'));
        }else{
            add_filter('the_content', array($this, 'getExitLeadboxCode'));
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

    public function displayPageSpecificTimedLeadbox(){
        $timed_embed_code = $this->leadboxApi->getSingleLeadbox($this->pageSpecificTimedLeadboxId);
        echo $timed_embed_code;
    }

    protected function getExitSpecifiExitLeadbox($post){
        $this->pageSpecificExitdLeadboxId = get_post_meta($post->ID, 'pageExitLeadbox', true);
        if(!empty($this->pageSpecificExitdLeadboxId)){
            $this->hasSpecificExit = true;
        }
    }

    public function displayPageSpecificExitLeadbox(){
        $exit_embed_code = $this->leadboxApi->getSingleLeadbox($this->pageSpecificExitdLeadboxId);
        echo $exit_embed_code;
    }

}