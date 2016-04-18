<?php


namespace Leadpages\models;


use Leadpages\Helpers\Security;

class LeadboxesModel
{

    public static function init()
    {
        add_action('admin_post_save_leadbox_options', array(get_called_class(), 'saveGlobalLeadboxes'));

    }

    public static function saveGlobalLeadboxes()
    {

        Security::checkAdminRefererStatic('save_leadbox_options');

        $globalLeadboxes = array(
            'lp_select_field_0' => sanitize_text_field($_POST['lp_select_field_0']),
            'leadboxes_timed_display_radio' => sanitize_text_field((!empty($_POST['leadboxes_timed_display_radio']) ? $_POST['leadboxes_timed_display_radio'] : '' )),
            'lp_select_field_2' => sanitize_text_field($_POST['lp_select_field_2']),
            'leadboxes_exit_display_radio' => sanitize_text_field((!empty($_POST['leadboxes_exit_display_radio']) ? $_POST['leadboxes_exit_display_radio'] : '' ))
        );

        static::updateLeadboxOption($globalLeadboxes);
        wp_redirect(admin_url().'?page=Leadboxes');
    }

    protected static function updateLeadboxOption($data){
        update_option('lp_settings', $data);
    }

    public static function getLpSettings(){
        $data = get_option('lp_settings');
        if(!is_wp_error($data)){
            return $data;
        }
    }

    public static function getCurrentTimedLeadbox(){
        $leadboxes = static::getLpSettings();
        if(!empty($leadboxes['lp_select_field_0'])){
            $currentTimedLeadbox = array($leadboxes['lp_select_field_0'], $leadboxes['leadboxes_timed_display_radio']);
        }else{
            $currentTimedLeadbox = array('none', 'none');
        }
        return $currentTimedLeadbox;
    }

    public static function getCurrentExitLeadbox(){
        $leadboxes = static::getLpSettings();
        if(!empty($leadboxes['lp_select_field_2'])){
            $currentExitLeadbox = array($leadboxes['lp_select_field_2'], $leadboxes['leadboxes_exit_display_radio']);
        }else{
            $currentExitLeadbox = array('none', 'none');
        }
        return $currentExitLeadbox;
    }

}