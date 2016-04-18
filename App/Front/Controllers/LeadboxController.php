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


    public function __construct(LeadboxApi $leadboxApi)
    {
        $this->leadboxApi = $leadboxApi;
    }

    public function initLeadboxes(){
        global $post;
        $this->setPageType($post);
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
        add_filter('the_content', array($this, 'getTimedLeadboxCode'));
        add_filter('the_content', array($this, 'getExitLeadboxCode'));
    }

}