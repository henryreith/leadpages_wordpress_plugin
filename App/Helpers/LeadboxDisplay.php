<?php

namespace Leadpages\Helpers;

trait LeadboxDisplay
{

    protected $postTypesForLeadboxes;

    /**
     * create a dropdown of every timed leadbox
     *
     * @param $leadboxArray
     *
     * @return string
     */
    public function timedDropDown($leadboxArray)
    {
        //echo '<pre>';print_r($leadboxArray);die();
        $select = "<select name='lp_select_field_0' id='leadboxesTime'>";
        $select .= "<option name='none' value='none'>None...</option>";

        foreach($leadboxArray['_items'] as $leadbox){

            if($leadbox['publish_settings']['time']['seconds'] > 0){
                $select .= "<option value=\"{$leadbox['id']}\"
                data-timeAppear=\"{$leadbox['publish_settings']['time']['seconds']}\"
                data-pageView=\"{$leadbox['publish_settings']['time']['views']}\"
                data-daysAppear=\"{$leadbox['publish_settings']['time']['days']}\">{$leadbox['name']}</option>";
            }
        }
        $select .="</select>";

        //echo $select;
        return $select;
    }

    /**
     * make a dropdown of every leadbox that has an exit time set on it
     *
     * @param $leadboxArray
     *
     * @return string
     */
    public function exitDropDown($leadboxArray)
    {
        //echo '<pre>';print_r($leadboxArray);die();
        $select = "<select name='lp_select_field_2' id='leadboxesExit'>";
        $select .= "<select name='none' value='none'>None...</select>";
        foreach($leadboxArray['_items'] as $leadbox){

            if($leadbox['publish_settings']['exit']['days'] > 0){
                $select .= "<option value=\"{$leadbox['id']}\"
                data-daysAppear=\"{$leadbox['publish_settings']['time']['days']}\">{$leadbox['name']}</option>";
            }
        }
        $select .="</select>";

        //echo $select;
        return $select;
    }


    /**
     * return a reduced list of all post types for leadboxes to be on
     *
     * @return array
     */
    public function getPostTypesForLeadboxes(){
        $postTypes = get_post_types();
        $unneededTypes = array('attachment', 'revision', 'nav_menu_item');
        $this->postTypesForLeadboxes = array_diff($postTypes, $unneededTypes);
    }

    /**
     *generate raido buttons for timed buttons
     */
    public function postTypesForTimedLeadboxes(){
        $this->getPostTypesForLeadboxes();
        $options = '<br />';
        $options .= '<input type="radio" id="timed_radio_all" name="leadboxes_timed_display_radio" value="all"> <label for="exit_radio_all">Every WordPress page, including homepage, 404 and posts</label>';

        foreach($this->postTypesForLeadboxes as $postType){
            $options .="<br />";
            $options .= '<input type="radio" id="timed_radio_'.$postType.'" name="leadboxes_timed_display_radio" value="'.$postType.'"> <label for="exit_'.$postType.'">Display on '.ucfirst($postType).'</label>';
        }

        return $options;
    }

    public function timedSettings(){

    }



}