<?php


namespace Leadpages\Helpers;


trait LeadpageType
{

    private static function get_front_lead_page() {
        $v = get_site_option( 'leadpages_front_page_id', false );

        return ( $v == '' ) ? false : $v;
    }

    public static function set_front_lead_page( $id ) {
        update_site_option( 'leadpages_front_page_id', $id );
    }

    public function is_front_page( $id ) {
        $front = self::get_front_lead_page();

        return ( $id == $front && $front !== false );
    }

    private static function get_wg_lead_page() {
        $v = get_site_option( 'leadpages_wg_page_id', false );

        return ( $v == '' ) ? false : $v;
    }

    public static function set_wg_lead_page( $id ) {
        update_site_option( 'leadpages_wg_page_id', $id );
    }

    private static function get_404_lead_page() {
        $v = get_site_option( 'leadpages_404_page_id', false );

        return ( $v == '' ) ? false : $v;
    }

    public static function set_404_lead_page( $id ) {
        update_site_option( 'leadpages_404_page_id', $id );
    }

}