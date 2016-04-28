<?php

namespace Leadpages\Helpers;

use Leadpages\models\LeadboxesModel;

trait LeadboxDisplay
{

    protected $postTypesForLeadboxes;
    protected $currentTimedLeadbox;
    protected $currentExitLeadbox;


    public function setCurrentLeadboxes(){
        $this->currentTimedLeadbox = LeadboxesModel::getCurrentTimedLeadbox();
        $this->currentExitLeadbox = LeadboxesModel::getCurrentExitLeadbox();
    }

    protected function currentTimedLeadbox($leadboxId){
        $this->setCurrentLeadboxes();
        if($this->currentTimedLeadbox[0] == $leadboxId ){
            return 'selected="selected"';
        }
    }

    protected function currentTimedLeadboxDisplayPostType($PostType){
        if($this->currentTimedLeadbox[1] == $PostType){
            return 'checked="checked"';
        }
    }

    protected function currentExitLeadbox($leadboxId){
        if($this->currentExitLeadbox[0] == $leadboxId){
            return 'selected="selected"';
        }
    }

    protected function currentExitLeadboxDisplayPostType($PostType){
        if($this->currentExitLeadbox[1] == $PostType){
            return 'checked="checked"';
        }
    }

    /**
     * create a dropdown of every timed leadbox
     *
     * @param $leadboxArray
     *
     * @return string
     */
    public function timedDropDown($leadboxArray)
    {
        $select = "<select name='lp_select_field_0' id='leadboxesTime'>";
        $select .= "<option name='none' value='none'". $this->currentTimedLeadbox('none') ." >None</option>";

        foreach($leadboxArray['_items'] as $leadbox){

            if(isset($leadbox['publish_settings']['time']) && $leadbox['publish_settings']['time']['seconds'] > 0){
                $select .= "<option value=\"{$leadbox['xor_hex_id']}\"
                data-timeAppear=\"{$leadbox['publish_settings']['time']['seconds']}\"
                data-pageView=\"{$leadbox['publish_settings']['time']['views']}\"
                data-daysAppear=\"{$leadbox['publish_settings']['time']['days']}\"
                ".$this->currentTimedLeadbox($leadbox['xor_hex_id'])."
                >{$leadbox['name']}</option>";
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
        $select .= "<option name='none' value='none' ". $this->currentExitLeadbox('none') .">None</option>";
        foreach($leadboxArray['_items'] as $leadbox){
            if(isset($leadbox['publish_settings']['exit']['days']) && $leadbox['publish_settings']['exit']['days'] >= 0){
                $days = $leadbox['publish_settings']['exit']['days'];
                $select .= "<option value=\"{$leadbox['xor_hex_id']}\"
                data-daysAppear=\"{$days}\"
                ".$this->currentExitLeadbox($leadbox['xor_hex_id']).">{$leadbox['name']}</option>";
            }else{
                $select .= "<option value=\"{$leadbox['xor_hex_id']}\"
                data-daysAppear=\"0\"
                ".$this->currentExitLeadbox($leadbox['xor_hex_id']).">{$leadbox['name']}</option>";
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

    protected function disallowedPostTypes(){
        $disallowedTypes = [
            'leadpages_post',
            //woocomerce
            'product_variation',
            'shop_order',
            'shop_order_refund',
            'shop_coupon',
            'shop_webhook',
        ];
        return $disallowedTypes;
    }

    /**
     *generate radio buttons for timed buttons
     */
    public function postTypesForTimedLeadboxes(){
        $this->getPostTypesForLeadboxes();
        $disallowedPostTypes = $this->disallowedPostTypes();
        $options = '<br />';
        $options .= '<input type="radio" id="timed_radio_all" name="leadboxes_timed_display_radio" value="all" '.$this->currentTimedLeadboxDisplayPostType('all').'> <label for="exit_radio_all">Every WordPress page, including homepage, 404 and posts</label>';

        foreach($this->postTypesForLeadboxes as $postType){
            if(in_array($postType, $disallowedPostTypes)){
                continue;
            }
            $options .="<br />";
            $options .= '<input type="radio" id="timed_radio_'.$postType.'" name="leadboxes_timed_display_radio" value="'.$postType.'" '.$this->currentTimedLeadboxDisplayPostType($postType).'> <label for="exit_'.$postType.'">Display on '.ucfirst($postType).'</label>';
        }

        return $options;
    }

    /**
     *generate radio buttons for timed buttons
     */
    public function postTypesForExitLeadboxes(){
        $this->getPostTypesForLeadboxes();
        $disallowedPostTypes = $this->disallowedPostTypes();
        $options = '<br />';
        $options .= '<input type="radio" id="timed_radio_all" name="leadboxes_exit_display_radio" value="all" '.$this->currentExitLeadboxDisplayPostType('all').'> <label for="exit_radio_all">Every WordPress page, including homepage, 404 and posts</label>';

        foreach($this->postTypesForLeadboxes as $postType){
            if(in_array($postType, $disallowedPostTypes)){
                continue;
            }
            $options .="<br />";
            $options .= '<input type="radio" id="timed_radio_'.$postType.'" name="leadboxes_exit_display_radio" value="'.$postType.'" '.$this->currentExitLeadboxDisplayPostType($postType).'> <label for="exit_'.$postType.'">Display on '.ucfirst($postType).'</label>';
        }

        return $options;
    }




        /*
      |--------------------------------------------------------------------------
      | Page Specific Leadbox methods
      |--------------------------------------------------------------------------

    */
    public function timedDropDownPageSpecific($leadboxArray, $post)
    {
        $select = "<select name='pageTimedLeadbox' id='leadboxesTime'>";
        $select .= "<option name='select' value='select'". $this->currentTimedLeadboxPageSpecific('select', $post->ID) ." >Select</option>";
        $select .= "<option name='none' value='none'". $this->currentTimedLeadboxPageSpecific('none', $post->ID) ." >None</option>";
        foreach($leadboxArray['_items'] as $leadbox){

            if(isset($leadbox['publish_settings']['time']) && $leadbox['publish_settings']['time']['seconds'] > 0){
                $select .= "<option value=\"{$leadbox['xor_hex_id']}\"
                data-timeAppear=\"{$leadbox['publish_settings']['time']['seconds']}\"
                data-pageView=\"{$leadbox['publish_settings']['time']['views']}\"
                data-daysAppear=\"{$leadbox['publish_settings']['time']['days']}\"
                ".$this->currentTimedLeadboxPageSpecific($leadbox['xor_hex_id'], $post->ID)."
                >{$leadbox['name']}</option>";
            }
        }
        $select .="</select>";

        //echo $select;
        return $select;
    }

    protected function currentTimedLeadboxPageSpecific($leadboxId, $postId){
        $pageSpecificLeadbox = get_post_meta($postId, 'pageTimedLeadbox', true);
        if($pageSpecificLeadbox == $leadboxId){
            return 'selected="selected"';
        }
    }

    public function exitDropDownPageSpecific($leadboxArray, $post)
    {
        $select = "<select name='pageExitLeadbox' id='leadboxesExit'>";
        $select .= "<option name='select' value='select'". $this->currentTimedLeadboxPageSpecific('select', $post->ID) ." >Select</option>";
        $select .= "<option name='none' value='none' ". $this->currentExitLeadboxPageSpecific('none', $post->ID) .">None</option>";
        foreach($leadboxArray['_items'] as $leadbox){

            if(isset($leadbox['publish_settings']['exit']['days']) && $leadbox['publish_settings']['exit']['days'] >= 0){
                $days = $leadbox['publish_settings']['exit']['days'];
                $select .= "<option value=\"{$leadbox['xor_hex_id']}\"
                data-daysAppear=\"{$days}\"
                ".$this->currentExitLeadboxPageSpecific($leadbox['xor_hex_id'], $post->ID).">{$leadbox['name']}</option>";
            }else{
                $select .= "<option value=\"{$leadbox['xor_hex_id']}\"
                data-daysAppear=\"0\"
                ".$this->currentExitLeadbox($leadbox['xor_hex_id']).">{$leadbox['name']}</option>";
            }
        }
        $select .="</select>";

        //echo $select;
        return $select;
    }

    protected function currentExitLeadboxPageSpecific($leadboxId, $postId){
        $pageSpecificLeadbox = get_post_meta($postId, 'pageExitLeadbox', true);

        if($pageSpecificLeadbox == $leadboxId){
            return 'selected="selected"';
        }
    }















    public function leadboesLoading(){
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-5 -15 120 120" width="150" height="150" fill="#14283a">
  <circle transform="translate(14 0)" cx="0" cy="14" r="0">
    <animate attributeName="r" values="0; 20; 0; 0" dur="1.2s" repeatCount="indefinite" begin="0"
      keytimes="0;0.2;0.7;1" keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.6 0.4 0.8" calcMode="spline" />
  </circle>
  <circle transform="translate(50 0)" cx="0" cy="14" r="0">
    <animate attributeName="r" values="0; 20; 0; 0" dur="1.2s" repeatCount="indefinite" begin="0.3"
      keytimes="0;0.2;0.7;1" keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.6 0.4 0.8" calcMode="spline" />
  </circle>
  <circle transform="translate(80 0)" cx="0" cy="14" r="0">
    <animate attributeName="r" values="0; 20; 0; 0" dur="1.2s" repeatCount="indefinite" begin="0.6"
      keytimes="0;0.2;0.7;1" keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.6 0.4 0.8" calcMode="spline" />
  </circle>
</svg>';
    }

}