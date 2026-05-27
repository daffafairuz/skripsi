<?php

namespace App\Listeners;

use App\Events\SiteDevicesUpdated;
use App\Services\MqttService;

class SyncSiteDevicesToMqtt
{
    /**
     * Handle the event.
     *
     * @param SiteDevicesUpdated $event
     * @return void
     */
    public function handle(SiteDevicesUpdated $event): void
    {
        MqttService::publishMasterSync($event->site);
    }
}
