<?php


namespace LeadpagesWP\Lib;

class LeadpagesCronJobs
{

    /**
     * Add scheduled times to cron schedules
     */
    public static function addCronScheduleTimes()
    {
        add_filter( 'cron_schedules', array(get_called_class(), 'add25DaySchedule'));
    }

    /**
     * Add 25 day schedule to cron schedule times
     *
     * @param $schedules
     *
     * @return mixed
     */
    public static function add25DaySchedule($schedules)
    {
        $schedules['25days'] = array(
          'interval' => 2160000,
          'display' => __('Every 25 Days')
        );
        return $schedules;
    }

    /**
     * Schedule Cron Jobs
     */
    public static function registerCronJobs()
    {
        //check users account to ensure its active
        if (! wp_next_scheduled ( 'check_user_leadpages_account' )) {
            wp_schedule_event(time(), 'hourly', 'check_user_leadpages_account');
        }

        //check users account to ensure its active
        if (! wp_next_scheduled ( 'refresh_leadpages_token' )) {
            wp_schedule_event(time(), '25days', 'refresh_leadpages_token');
        }

        add_action('check_user_leadpages_account', array(get_called_class(), 'checkUsersAccountStatus'));
        add_action('refresh_leadpages_token', array(get_called_class(), 'updateUsersSecurityToken'));

    }

    /**
     * Unregister all scheduled crons
     */
    public static function unregisterCronJobs()
    {
        wp_clear_scheduled_hook('check_user_leadpages_account');
        wp_clear_scheduled_hook('refresh_leadpages_token');
    }

    public static function checkUsersAccountStatus()
    {
        global $leadpagesApp;
        $loginService = $leadpagesApp['leadpagesLogin'];

        //get the token to makesure it is set
        $loginService->getToken();
        if($loginService->token !='') {
            $response = $leadpagesApp['leadpagesLogin']->checkCurrentUserSession();
            if(!array_key_exists('LEADPAGES_20160216' ,$response['profiles'])){
                $loginService->deleteToken();
            }
        }
    }

    public static function updateUsersSecurityToken()
    {
        global $leadpagesApp;
        $loginService = $leadpagesApp['leadpagesLogin'];

        if($loginService->token !='') {
            $response = $leadpagesApp['leadpagesLogin']->refreshUserToken();
            $loginService->token = $response['securityToken'];
            $loginService->storeToken();
        }
    }

}