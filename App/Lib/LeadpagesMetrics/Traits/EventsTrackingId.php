<?php


namespace LeadpagesMetrics\Traits;

use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;

trait EventsTrackingId
{

    public $eventsTrackingId;

    /**
     * Generate and Store UUID for Tracking Purposes
     */
    public function generateEventsTrackingId()
    {
        try {

            //generate tracking id
            $trackingId = Uuid::uuid1();
            $this->eventsTrackingId = $trackingId->toString();

            //store tracking id
            $this->storeTrackingIdWordPress();

        } catch (UnsatisfiedDependencyException $e) {
            // Some dependency was not met. Either the method cannot be called on a
            // 32-bit system, or it can, but it relies on Moontoast\Math to be present.
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }

    }
//
    public function storeTrackingIdWordPress()
    {
        update_option('lp-events-tracking-id', $this->eventsTrackingId);
    }
//
    public function getEventsTrackingId()
    {
        if(!$this->eventsTrackingId = get_option('lp-events-tracking-id')){
            $this->generateEventsTrackingId();
        }
        return $this->eventsTrackingId;
    }

}