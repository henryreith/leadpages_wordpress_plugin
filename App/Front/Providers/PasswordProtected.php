<?php


namespace Leadpages\Front\Providers;


class PasswordProtected
{
    public $postPassword = array();
    public $submittedPassword;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getPostPassword($post){

        $postId = $post->ID;
        $this->postPassword = $this->db->get_row( "SELECT post_password FROM {$this->db->posts} WHERE ID = {$postId}" );
        if(strlen($this->postPassword->post_password) > 0 || !is_null($this->postPassword->post_password)){
            return $this->postPassword->post_password;
        }else{
            return false;
        }
    }

    public function checkWPPasswordHash($post, $COOKIEHASH){
        global $wp_hasher;
        if ( empty( $wp_hasher ) ) {
            require_once( ABSPATH . 'wp-includes/class-phpass.php' );
            $wp_hasher = new \PasswordHash(8, true);
        }

        $password = $this->getPostPassword($post);
        //$hash = 	$wp_hasher->HashPassword($password);
        //echo $hash; die();
        if ( isset( $_COOKIE['wp-postpass_' . $COOKIEHASH] ) ){
            return true;
        }else{
            return false;
        }
    }
    
}