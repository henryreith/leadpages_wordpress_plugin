<?php


namespace Leadpages\admin\SettingsPages;

use TheLoop\Contracts\SettingsPage;

class LeadpagesLoginPage implements SettingsPage
{
    public static function getName(){
        return get_called_class();
    }

    public function definePage() {
        global $leadpagesConfig;
        add_menu_page('leadpages', 'Leadpages', 'manage_options', 'Leadpages', array($this, 'displayCallback'), $leadpagesConfig['admin_images'].'/menu-icon.png' );
    }

    public function displayCallback(){

        ?>
        <link rel="stylesheet" href="https://4-0-2-dot-leadpage-test.appspot.com/static/lp10016456456456/bootstrap/lp3/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://4-0-2-dot-leadpage-test.appspot.com/static/lp10016456456456/build/css/lp.css" />
        <link rel="stylesheet" href="https://4-0-2-dot-leadpage-test.appspot.com/static/lp10016456456456/min/jquery-ui-1.9.2.custom.min.css" type="text/css" />

        <div class="lego-login__body" style="margin-left:-20px !important;">
            <div class="lego-login lego-base">

                <div class="lego-login__left">

                    <div class="lp-login-asset" style="display:block !important;">
                        <p class="intro">Introducing</p>
                        <p class="heading">Center by Leadpages:<br>
                            A Command Center For Your Digital Marketing</p>
                        <hr>
                        <p class="light-text sub-heading">Automate tasks across the tools you want use. Save time. Eliminate hassle. Grow your business.</p>
                        <a href="https://www.center.io/blog/" target="_blank">
                            <button class="lego-btn">SEE HOW</button>
                        </a>
                        <img class="sidebar-logo" src="https://4-0-2-dot-leadpage-test.appspot.com/static/lp10016456456456/img/center/center_white_2x.png">
                    </div>
                    <div class="center-login-asset">
                        <p class="heading">Use Leadpages for Your Next Campaign.</p>
                        <hr>

                        <p class="light-text sub-heading">Take the guesswork out of creating great looking landing pages that drive results.</p>

                        <a href="http://leadpages.net" target="_blank">
                            <button class="lego-btn">SEE HOW</button>
                        </a>
                    </div>
                </div>

                <div class="lego-login__right" style="flex-grow:0; margin-left:auto; margin-right:auto;">

                    <!-- MOCK OBJECT FOR FLEXBOX HACK -->
                    <div></div>
                    <!-- END MOCK OBJECT -->
                    <div>
                        <img class="lp-logo center-login-asset" src="https://4-0-2-dot-leadpage-test.appspot.com/static/lp10016456456456/img/logos/center_logo_beta.svg" />

                        <img class="lp-logo lp-login-asset" src="https://4-0-2-dot-leadpage-test.appspot.com/static/lp10016456456456/img/logos/logo_standard.svg" />
                        <div id="login-form">
                            <form novalidate="novalidate" class="form" name="login-form" method="post" action="admin-post.php">

                                <br/>
                                <!-- FORM FIELDS -->
                                <div class="form__field-container">
                                    <label class="form__label">Username</label>
                                    <input class="required email form__field" type="email" name="username" placeholder="Email" required/>
                                </div>
                                <div class="form__field-container">
                                    <label class="form__label">Password</label>
                                    <input class="required form__field" type="password" name="password" placeholder="Password" />
                                    <span class="form-control-feedback"></span>
                                </div>
                                <!-- both of these need to be added to make login form work in wordpress -->
                                <input type="hidden" name="action" value="leadpages_login_form" />
                                <?php wp_nonce_field( 'leadpages_login' ); ?>

                                <!-- FORM FIELDS END -->

                                <!-- SIGN IN CONTAINER -->
                                <div class="lego-login__button-container">
                                    <button name="form-submit" class="lego-btn lego-btn--icon-right" type="submit" id="lego-login-submit" data-lp-icon="&#xe204;" data-loading-text="Logging in &hellip;">Sign In</button>
                                </div>
                                <!-- SIGN IN END -->
                                <hr/>

                                <!-- GET LEADPAGES  -->
                                <div class="lego-login__button-container">
                                    <a href="#">Need an Account?</a>
                                    <a href="http://www.leadpages.net/pricing?utm_source=testing_page&utm_medium=link&utm_campaign=test_from_engineering" class="lego-btn lego-btn--outlined" target="_blank">Get Leadpages&reg;</a>
                                </div>

                            </form>
                            <!-- GET LEADPAGES END -->
                        </div>
                    </div>
                    <div class="lego-login__message">
                        <div class="lp-login-asset">
                            <img class="img-responsive" src="https://4-0-2-dot-leadpage-test.appspot.com/static/lp10016456456456/img/login/sidebar_center-ad@thumb.jpg"/>
                            <div>
                        <span>Introducing Center by Leadpages:<br>
                        A Command Center For Your Digital Marketing</span>
                                <p>Automate tasks across the tools you want to use. Save time. Eliminate hassles. Grow your business.</p>
                                <div class="see-how">
                                    <a href="https://www.center.io/blog/" target="_blank" class="link link--alt">See How</a>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="lego-login__footer">
                <span class="lp-login-asset copyright">&copy; 2010-2016 Avenue 81 Inc. d/b/a Leadpages, all rights reserved. <a href="http://www.leadpages.net/legal/" target="_blank">Terms of Service</a><br>
Leadpages&reg;, Leadbox&reg;, Leadboxes&reg;, Leadlinks&reg;, Leaddigits&reg; are registered trademarks of Avenue 81 Inc. d/b/a Leadpages.<br></span>
                        <span class="center-login-asset copyright">&copy; 2010-2016 Avenue 81 Inc., all rights reserved.<br> Center&reg; is a registered trademark of Avenue 81 Inc.</span>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="https://4-0-2-dot-leadpage-test.appspot.com/static/lp10016456456456/min/dist/jquery.js"></script>
        <!-- LEADPAGES LOGIN PAGE -->

        <?php
    }

    public function registerPage(){
        add_action( 'admin_menu', array($this, 'definePage') );
    }

}