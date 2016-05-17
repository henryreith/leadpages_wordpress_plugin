<?php


namespace Leadpages\Admin\MetaBoxes;

use TheLoop\Contracts\MetaBox;
use Leadpages\Helpers\LeadboxDisplay;
use TheLoop\ServiceContainer\ServiceContainerTrait;

class LeadboxMetaBox implements MetaBox
{
    use ServiceContainerTrait;
    use LeadboxDisplay;

    private $ioc;

    public function __construct()
    {
        $this->ioc = $this->getContainer();

    }

    public static function getName(){
        return get_called_class();
    }

    public function defineMetaBox()
    {
        add_meta_box("leadbox-select", "Page Specific Leadbox", array($this, 'callback'), '', "side", "low", null);
    }

    public function callBack($post, $box)
    {

        $leadboxes = $this->ioc['leadboxApi']->getLeadBoxes();
        echo "<p>Set a specific Leadbox&reg; to display on this page only. This will override any global Leadboxes&reg that are
        setup.</p>";
        echo "<div id='pageSpecificLeadbox'>";
        echo "<label><strong>Timed Leadboxes&reg</strong></label>";
        echo '<br />';
        echo $this->timedDropDownPageSpecific($leadboxes, $post);
        echo '<br />';
        echo "<label><strong>Exit Leadboxes&reg</strong></label>";
        echo '<br />';
        echo $this->exitDropDownPageSpecific($leadboxes, $post);
        echo "</div>";

    }

    public function removeMetaBoxFromLeadpagePostType(){
      remove_meta_box( 'leadbox-select' , 'leadpages_post', 'side'  );
    }

    public function registerMetaBox()
    {
        add_action('add_meta_boxes', array($this, 'defineMetaBox'));
        add_action( 'add_meta_boxes', array($this, 'removeMetaBoxFromLeadpagePostType'));

    }


}