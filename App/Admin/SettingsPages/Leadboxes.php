<?php


namespace Leadpages\admin\SettingsPages;

use TheLoop\Contracts\SettingsPage;
use Leadpages\models\LeadboxesModel;
use Leadpages\Helpers\LeadboxDisplay;
use TheLoop\ServiceContainer\ServiceContainerTrait;

class Leadboxes implements SettingsPage
{

    use ServiceContainerTrait;
    use LeadboxDisplay;

    public static function getName(){
        return get_called_class();
    }

    public function definePage() {
        global $config;

        add_action( 'admin_enqueue_scripts', array($this, 'leadboxScripts') );

        add_menu_page('leadboxes', 'Leadboxes', 'manage_options', 'Leadboxes', array($this, 'displayCallback'), $config['admin_images'].'/leadboxes_sm.png' );
    }

    public function displayCallback(){
        ?>
            <div id="leadboxesLoading">
                <?php echo $this->leadboesLoading(); ?>
            </div>
        <div id="leadboxesForm" style="display:none">
            <form action="admin-post.php" method="post">

                <h2>Configure Leadboxes&reg</h2>
                <p>Here you can setup timed and exit Leadboxes&reg;. If you want to place a Leadbox&trade; via link, button, or image to any page, you need to copy and paste the HTML code you'll find in the Leadbox&trade; publish interface inside the Leadpages&trade; application.</p>
                <div id="leadbox-options">
                    <div id="timed-leadboxes">
                        <h2>Timed Leadbox&trade; Configuration</h2>
                        <p>All your LeadBoxes&reg; with Timed configuration are listed below. Go to our <a href="https://my.leadpages.net" target="_blank"> application </a>   to save or edit Timed settings for your LeadBoxes&reg;</p>
                        <div class="timedLeadboxes">
                            <label for="timed-leadboxes"><h3 style="display:inline;">Timed Lead Boxes:</h3></label>
                             <div class="timeLeadBoxes"></div>
                            <div class="postTypesForTimedLeadbox"></div>
                            <div id="selectedLeadboxSettings"></div>
                        </div>
                    </div>
                    <hr />
                    <div id="timed-leadboxes">
                        <h2>Exit Leadbox&trade; Configuration</h2>
                        <p>All your Leadboxes&reg; are listed below. Any LeadBoxes&reg; without Exit configuration will default to display every time a user visits your page. Go to our <a href="https://my.leadpages.net" target="_blank"> application </a> to use your own settings.</p>
                        <div class="exitLeadboxes">
                            <label for="timed-leadboxes"><h3 style="display:inline;">Exit Lead Boxes:</h3></label>
                            <div class="exitLeadBoxes"></div>
                            <div class="postTypesForExitLeadbox"></div>
                            <div id="selectedExitLeadboxSettings"></div>
                        </div>
                    </div>
                </div>


                <input type="hidden" name="action" value="save_leadbox_options" />
                <?php wp_nonce_field( 'save_leadbox_options' ); ?>
                <input type="submit" value="Save Global Leadboxes" class="leadboxButton">
            </form>
        </div>
        <?php

    }

    public function registerPage(){
        add_action( 'admin_menu', array($this, 'definePage') );

    }


    public function leadboxScripts(){
        global $config;
        $ioc = $this->getContainer();
        $leadboxes = $ioc['leadboxApi']->getLeadBoxes();
        wp_enqueue_script('Leadboxes', $config['admin_assets'] . '/js/Leadboxes.js', array('jquery'));
        wp_localize_script('Leadboxes', 'leadboxes_object', array(
          'ajax_url'  => admin_url('admin-ajax.php'),
          'timedLeadboxes' => $this->timedDropDown($leadboxes),
          'postTypesForTimedLeadboxes' => $this->postTypesForTimedLeadboxes(),
          'postTypesForExitLeadboxes' => $this->postTypesForExitLeadboxes(),
          'exitLeadboxes'  => $this->exitDropDown($leadboxes),
    ));
    }


    public function allLeadboxesAjax(){
        $ioc = $this->getContainer();
        $allLeadBoxes = $ioc['leadboxApi']->getLeadBoxes();

    }
}