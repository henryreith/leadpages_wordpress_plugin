<?php


namespace Leadpages\admin\SettingsPages;

use TheLoop\Contracts\SettingsPage;

class LeadpagesLoginPage implements SettingsPage
{

    public function definePage() {
        global $config;
        add_menu_page('leadpages', 'Leadpages', 'manage_options', 'Leadpages', array($this, 'displayCallback'), $config['admin_images'].'/menu-icon.png' );
    }

    public function displayCallback(){

        ?>
        <link href='//fonts.googleapis.com/css?family=Montserrat:700' rel='stylesheet' type='text/css'>
        <link href='//fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://my.leadpagestest.net/static/lp993/bootstrap/lp3/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://my.leadpagestest.net/static/lp993/bootstrap/lp3/css/font-awesome.min.css" />
        <link rel="stylesheet" href="https://my.leadpagestest.net/static/lp993/build/css/lp.css" />
        <link rel="stylesheet" href="https://my.leadpagestest.net/static/lp993/min/jquery-ui-1.9.2.custom.min.css" type="text/css" />
        <link rel="stylesheet" href="https://my.leadpagestest.net/static/lp993/min/font-awesome.min.css" />
        <div id="login-wrapper" style="margin-left:-10px;">
            <div class="container">
                <div class="row login-header no-gutter">
                    <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 no-gutter">
                        <a class="btn btn-secondary pull-right" href="https://www.leadpages.net/pricing/">Get LeadPages&trade;</a>
                        <img alt="logo" src="https://my.leadpagestest.net/static/lp993/img/lp-login-logo.png">
                    </div>
                </div>


                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 login-box" id="login-form">
                        <form novalidate="novalidate" class="form" name="login-form" method="post" action="admin-post.php">
                            <fieldset>
                                <h2 class="text-center">Login to Your Account</h2>
                                <div class="col-md-12">
                                    <div name="alert"></div>
                                    <br>
                                </div>
                                <div class="form-group col-md-12">
                                    <input type="email" required="" placeholder="Email" name="username" class="form-control required email" aria-required="true" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAAAXNSR0IArs4c6QAAALZJREFUOBFjYKAANDQ0rGWiQD9IqzgL0BQ3IKMXiB8AcSKQ/waIrYDsKUD8Fir2pKmpSf/fv3+zgPxfzMzMSbW1tbeBbAaQC+b+//9fB4h9gOwikCAQTAPyDYHYBciuBQkANfcB+WZAbPP37992kBgIUOoFBiZGRsYkIL4ExJvZ2NhAXmFgYmLKBPLPAfFuFhaWJpAYEBQC+SeA+BDQC5UQIQpJYFgdodQLLyh0w6j20RCgUggAAEREPpKMfaEsAAAAAElFTkSuQmCC&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;">
                                    <span class="form-control-feedback"></span>
                                </div>
                                <div class="form-group col-md-12">
                                    <input type="password" placeholder="Password" name="password" class="form-control required" aria-required="true" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAAAXNSR0IArs4c6QAAALZJREFUOBFjYKAANDQ0rGWiQD9IqzgL0BQ3IKMXiB8AcSKQ/waIrYDsKUD8Fir2pKmpSf/fv3+zgPxfzMzMSbW1tbeBbAaQC+b+//9fB4h9gOwikCAQTAPyDYHYBciuBQkANfcB+WZAbPP37992kBgIUOoFBiZGRsYkIL4ExJvZ2NhAXmFgYmLKBPLPAfFuFhaWJpAYEBQC+SeA+BDQC5UQIQpJYFgdodQLLyh0w6j20RCgUggAAEREPpKMfaEsAAAAAElFTkSuQmCC&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;">
                                    <span class="form-control-feedback"></span>
                                </div>
                            </fieldset>
                            <div class="col-md-12">
                                <input type="submit" value="Login" class="btn btn-large btn-primary btn-block" name="form-submit">
                            </div>
                            <input type="hidden" name="action" value="leadpages_login_form" />
                            <?php wp_nonce_field( 'leadpages_login' ); ?>
                        </form>
                    </div>
                </div>


                <div class="footer-content text-center">
                    <ul class="list-inline text-uppercase text-reduced nav-links">
                        <li><a id="blog" target="_blank" href="http://blog.leadpages.net">Blog</a></li>
                        <li><a id="getting-started" target="_blank" href="https://my.leadpages.net/getting-started/">Getting Started</a></li>
                        <li><a id="support" href="https://my/leadpages.net/support/">Support</a></li>
                        <li><a id="terms-of-service" href="http://www.leadpages.net/legal/">Terms of Service</a></li>
                    </ul>
            <span class="copy-block">
                &copy; <?php echo date('Y'); ?> LeadPages.net. <a id="legal-info" class="hi" target="_blank" href="http://www.leadpages.net/legal/">Legal Information</a>
            </span>
                </div>
            </div>
        </div>
        <?php
    }

    public function registerPage(){
        add_action( 'admin_menu', array($this, 'definePage') );
    }

}