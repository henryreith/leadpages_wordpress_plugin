<?php


namespace LeadpagesWP\Helpers;


class LeadpageType
{

    public static function get_front_lead_page() {
        $v = get_option( 'leadpages_front_page_id', false );

        return ( $v == '' ) ? false : $v;
    }

    public static function set_front_lead_page( $id ) {
        update_option( 'leadpages_front_page_id', $id );
    }

    public static function is_front_page( $id ) {
        $front = self::get_front_lead_page();

        return ( $id == $front && $front !== false );
    }

    public static function get_wg_lead_page() {
        $v = get_option( 'leadpages_wg_page_id', false );

        return ( $v == '' ) ? false : $v;
    }

    public static function set_wg_lead_page( $id ) {
        update_option( 'leadpages_wg_page_id', $id );
    }

    public static function get_404_lead_page() {
        $v = get_option( 'leadpages_404_page_id', false );

        return ( $v == '' ) ? false : $v;
    }

    public static function set_404_lead_page( $id ) {
        update_option( 'leadpages_404_page_id', $id );
    }

    public static function is_nf_page($id){
        $nf = self::get_404_lead_page();

        return ( $id == $nf && $nf !== false );
    }

}