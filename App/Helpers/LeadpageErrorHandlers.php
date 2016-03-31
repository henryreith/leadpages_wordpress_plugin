<?php

namespace Leadpages\Helpers;

trait LeadpageErrorHandlers
{

    public static function format_error( $msg ) {
        return <<<EOT
<!DOCTYPE html>
<html>
  <head>
    <title>LeadPages&trade; Alert</title>
    <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css" rel="stylesheet">
    <style type="text/css">
      body { padding-top: 40px; padding-bottom: 40px; background-color: #f5f5f5; }
      .error-box { max-width: 300px; padding: 19px 29px 29px; margin: 0 auto 20px; background-color: #fff; border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05); -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05); box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
    </style>
  </head>
  <body>
    <div class="container error-box">
        <h3><a href="https://www.leadpages.net/">LeadPages&trade;</a> Alert</h3>
        <div class="alert alert-error">$msg</div>
        <div>
            <a href="http://www.leadpages.net/">LeadPages&trade;</a>
            <a href="https://support.leadpages.net/" class="pull-right">Support</a>
        </div>
    </div>
  </body>
</html>
EOT;
    }


    public static function homePageExists($post_id){

        $homepage = LeadpageType::get_front_lead_page($post_id);
        if(!$homepage){
            return false;
        }

        if($homepage != $post_id){
            $msg = __('Post id '.$homepage.' is already a homepage.<br />
            Please remove it to publish page.<br />
            You may save this post and a draft.');
            $error = self::format_error($msg);
            echo $error;
            die();
        }

    }

    public static function welcomegatePageExists($post_id){

        $welcomegate = LeadpageType::get_wg_lead_page($post_id);
        if(!$welcomegate){
            return false;
        }

        if($welcomegate != $post_id){
            return true;
            $msg = __('Post id '.$welcomegate.' is already a Welcome Gate&trade;.
            Please remove it to publish page.
            You may save this post and a draft.');
            $error = self::format_error($msg);
            echo $error;
            die();
        }

    }

    public static function nfPageExists($post_id){

        $nf = LeadpageType::get_404_lead_page($post_id);
        if(!$nf){
            return false;
        }

        if($nf != $post_id){
            $msg = __('Post id '.$nf.' is already a 404 Page.
            Please remove it to publish page.
            You may save this post and a draft.');
            $error = self::format_error($msg);
            echo $error;
            die();
        }

    }

    /**
     * check is post type already exist
     *
     * @param $post_id
     * @param $post
     */
    public static function checkPageTypeExists($postType, $post){

        if($post->post_status == 'publish') {
            switch ($postType) {
                case 'fp':
                    self::homePageExists($post->ID);
                    break;
                case 'wg':
                   return self::welcomegatePageExists($post->ID);
                    break;
                case 'nf':
                    self::nfPageExists($post->ID);
                    break;
            }
        }
    }
}