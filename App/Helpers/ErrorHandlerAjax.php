<?php

use Leadpages\Helpers\LeadpageType;

function homePageExists()
{
    //remove transients from last save
    delete_transient('page_save_error_type_exists');
    delete_transient('page_save_error_type_exists_id');

    $page = LeadpageType::get_front_lead_page();

     if(!$page){
         die('success');
     }
    $newPageId = $_POST['pageId'];
    if($newPageId != $page){
        die('error');
    }else{
        die('success');
    }
}

function welcomeGatePageExists()
{
    $page = LeadpageType::get_wg_lead_page();

    if(!$page){
        die('success');
    }
    $newPageId = $_POST['pageId'];
    if($newPageId != $page){
        die('error');
    }else{
        die('success');
    }
}

add_action( 'wp_ajax_homePageExists', 'homePageExists' );
add_action( 'wp_ajax_welcomeGatePageExists', 'welcomeGatePageExists' );

