<?php


namespace Leadpages\Helpers;


Class Security
{

    public function userPrivilege($role){
        if ( ! \current_user_can( $role ) )
        {
            die( 'You are not allowed to be on this page.' );
        }
    }

    public function checkAdminReferer( $nonce ){

        check_admin_referer( 'leadpages_login' );
    }
}